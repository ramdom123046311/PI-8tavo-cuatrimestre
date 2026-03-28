from flask import Flask, render_template, request, redirect, url_for, session, flash, Response
from functools import wraps
import requests
import os

app = Flask(__name__)
app.secret_key = os.getenv('FLASK_SECRET_KEY', 'salud_maternal_flask_secret_2024')

# URL base de la FastAPI
API_URL = os.getenv("API_URL", "http://127.0.0.1:8001/api")


# ── Helpers ──────────────────────────────────────────────────────────────────

def get_auth_headers():
    """Devuelve cabecera Authorization si hay token en sesión."""
    token = session.get('token')
    if token:
        return {'Authorization': f'Bearer {token}'}
    return {}


def api_get(path, **kwargs):
    """GET a la API con manejo de errores centralizado."""
    try:
        r = requests.get(f"{API_URL}{path}", headers=get_auth_headers(), timeout=8, **kwargs)
        if r.status_code == 200:
            return r.json(), None
        return None, f"Error {r.status_code}: {r.text}"
    except requests.exceptions.ConnectionError:
        return None, "No se pudo conectar con la API."
    except requests.exceptions.Timeout:
        return None, "La API tardó demasiado en responder."
    except Exception as e:
        return None, str(e)


def api_post(path, json_data=None, files=None, data=None):
    """POST a la API con manejo de errores centralizado."""
    try:
        r = requests.post(
            f"{API_URL}{path}",
            json=json_data,
            files=files,
            data=data,
            headers=get_auth_headers(),
            timeout=8
        )
        return r.status_code, r.json() if r.content else {}
    except requests.exceptions.ConnectionError:
        return 503, {"detail": "No se pudo conectar con la API."}
    except requests.exceptions.Timeout:
        return 504, {"detail": "La API tardó demasiado en responder."}
    except Exception as e:
        return 500, {"detail": str(e)}


# ── Decoradores ───────────────────────────────────────────────────────────────

def login_required(f):
    """Redirige al login si el usuario no ha iniciado sesión."""
    @wraps(f)
    def decorated(*args, **kwargs):
        if 'token' not in session:
            flash("Debes iniciar sesión para acceder a esta página.")
            return redirect(url_for('login'))
        return f(*args, **kwargs)
    return decorated


def doctor_required(f):
    """Sólo accesible para usuarios con rol de médico (id_rol=2)."""
    @wraps(f)
    def decorated(*args, **kwargs):
        if 'token' not in session:
            flash("Debes iniciar sesión.")
            return redirect(url_for('login'))
        if session.get('rol') != 2:
            flash("Acceso no autorizado.")
            return redirect(url_for('patient_dashboard'))
        return f(*args, **kwargs)
    return decorated


def patient_required(f):
    """Sólo accesible para usuarios con rol de paciente (id_rol=1)."""
    @wraps(f)
    def decorated(*args, **kwargs):
        if 'token' not in session:
            flash("Debes iniciar sesión.")
            return redirect(url_for('login'))
        if session.get('rol') not in (1, None):
            flash("Acceso no autorizado.")
            return redirect(url_for('doctor_dashboard'))
        return f(*args, **kwargs)
    return decorated


# ── Auth Routes ───────────────────────────────────────────────────────────────

@app.route("/")
def home():
    if 'token' in session:
        if session.get('rol') == 2:
            return redirect(url_for('doctor_dashboard'))
        return redirect(url_for('patient_dashboard'))
    return redirect(url_for('login'))


@app.route('/login', methods=['GET', 'POST'])
def login():
    # Si ya está autenticado, redirigir
    if 'token' in session:
        return redirect(url_for('home'))

    if request.method == 'POST':
        correo = request.form.get('correo', '').strip()
        contrasena = request.form.get('contrasena', '')

        if not correo or not contrasena:
            flash("Por favor completa todos los campos.")
            return render_template('login.html')

        try:
            r = requests.post(
                f"{API_URL}/login",
                json={"correo": correo, "contrasena": contrasena},
                timeout=8
            )

            if r.status_code == 200:
                data = r.json()
                user_info = data.get('user', {})

                session['token']   = data.get('access_token')
                session['user_id'] = user_info.get('id')
                session['rol']     = user_info.get('rol')
                
                nombre_base = user_info.get('nombre', 'Usuario')
                apellido_base = user_info.get('apellido', '')
                session['nombre']  = f"{nombre_base} {apellido_base}".strip()
                session['correo']  = correo
                session['tiene_foto'] = user_info.get('tiene_foto', False)

                if session['rol'] == 2:
                    return redirect(url_for('doctor_dashboard'))
                return redirect(url_for('patient_dashboard'))

            elif r.status_code == 401:
                flash("Credenciales incorrectas. Verifica tu correo y contraseña.")
            elif r.status_code == 400:
                detail = r.json().get('detail', '')
                if 'inactive' in detail.lower():
                    flash("Tu cuenta está suspendida. Contacta al administrador.")
                else:
                    flash(f"Error al iniciar sesión: {detail}")
            else:
                flash(f"Error inesperado ({r.status_code}). Intenta de nuevo.")

        except requests.exceptions.ConnectionError:
            flash("No se pudo conectar con el servidor. Verifica que el servicio esté activo.")
        except requests.exceptions.Timeout:
            flash("El servidor tardó demasiado en responder. Intenta más tarde.")
        except Exception as e:
            flash(f"Error inesperado: {str(e)}")

    return render_template('login.html')


@app.route('/logout')
def logout():
    session.clear()
    flash("Has cerrado sesión correctamente.")
    return redirect(url_for('login'))


@app.route('/registro', methods=['GET', 'POST'])
def registro():
    if request.method == 'POST':
        datos = {
            "nombre":            request.form.get('nombre', '').strip(),
            "apellido_paterno":  request.form.get('apellido_paterno', '').strip(),
            "genero":            request.form.get('genero'),
            "fecha_nacimiento":  request.form.get('fecha_nacimiento'),
            "telefono":          request.form.get('telefono', '').strip(),
            "correo":            request.form.get('correo', '').strip(),
            "contrasena":        request.form.get('contrasena'),
            "id_rol":            int(request.form.get('id_rol', 1))
        }

        # Validaciones básicas
        if not all([datos['nombre'], datos['apellido_paterno'], datos['correo'], datos['contrasena']]):
            flash("Por favor completa todos los campos obligatorios.")
            return render_template('registro_usuario.html')
            
        # Validación de formato y longitud de teléfono
        telefono_str = str(datos.get('telefono', ''))
        if not telefono_str.isdigit() or len(telefono_str) != 10:
            flash("El número de teléfono debe tener exactamente 10 dígitos numéricos.")
            return render_template('registro_usuario.html')

        # Add doctor fields if it is a doctor
        if datos['id_rol'] == 2:
            cp = request.form.get('cp', '').strip()
            
            if not cp.isdigit() or len(cp) != 11:
                flash("La cédula profesional debe tener exactamente 11 dígitos numéricos.")
                return render_template('registro_usuario.html')
                
            datos['cp'] = cp
            datos['especialidad'] = request.form.get('especialidad', '').strip()
            datos['anios_experiencia'] = request.form.get('anios_experiencia', 0)

        status_code, resp = api_post("/usuarios", json_data=datos)
        if status_code == 200:
            if datos['id_rol'] == 2:
                flash("¡Registro exitoso! Tu cuenta como médico está en revisión por el administrador.")
            else:
                flash("¡Registro exitoso! Ya puedes iniciar sesión.")
            return redirect(url_for('login'))
        elif status_code == 400:
            detail = resp.get('detail', 'El correo ya está registrado.')
            flash(f"Error al registrar: {detail}")
        else:
            flash("Error al conectar con el servidor.")

    return render_template('registro_usuario.html')


# ── Patient Routes ────────────────────────────────────────────────────────────

@app.route('/patient/dashboard')
@login_required
def patient_dashboard():
    total_citas = 0
    try:
        user_id = session.get('user_id')
        citas_data, err = api_get("/citas", params={"id_usuario": user_id})
        if citas_data:
            total_citas = len(citas_data)
    except Exception:
        pass
    return render_template('patient/dashboard.html', total_citas=total_citas)


@app.route('/patient/specialists')
@login_required
def patient_specialists():
    medicos, err = api_get("/medicos/con-nombre", params={"solo_activos": "true"})
    if err:
        flash(f"No se pudo cargar la lista de especialistas: {err}")
    return render_template('patient/specialists.html', medicos=medicos or [])


@app.route('/patient/appointments', methods=['GET', 'POST'])
@login_required
def patient_appointments():
    user_id = session.get('user_id')

    if request.method == 'POST':
        hora_raw = request.form.get('hora', '')
        # Asegurar formato HH:MM:SS
        hora = hora_raw + ":00" if len(hora_raw) == 5 else hora_raw

        medico_id = request.form.get('id_medico', '')
        if not medico_id:
            flash("Error: debes seleccionar un especialista.")
            return redirect(url_for('patient_appointments'))

        datos_cita = {
            "id_usuario":         user_id,
            "id_medico":          int(medico_id),
            "fecha":              request.form.get('fecha'),
            "hora":               hora,
            "motivo_cancelacion": request.form.get('motivo', ''),  # reutilizado para motivo de consulta
        }
        status_code, resp = api_post("/citas", json_data=datos_cita)
        if status_code == 200:
            flash("✅ ¡Cita solicitada exitosamente! Será visible en 'Mis Citas'.")
        else:
            detail = resp.get('detail', 'Error al guardar la cita.')
            flash(f"❌ Error al solicitar la cita: {detail}")
        return redirect(url_for('patient_appointments'))

    # GET — cargar médicos y citas del usuario actual (filtrado en servidor)
    medicos_data, _ = api_get("/medicos/con-nombre", params={"solo_activos": "true"})
    medicos = medicos_data or []

    citas_data, err = api_get("/citas", params={"id_usuario": user_id})
    citas_usuario = citas_data or []

    # Enriquecer citas con nombre del médico
    medicos_map = {m['id_medico']: m['nombre_completo'] for m in medicos}
    for c in citas_usuario:
        c['nombre_medico'] = medicos_map.get(c.get('id_medico'), f"Médico {c.get('id_medico')}")

    return render_template('patient/appointments.html', citas=citas_usuario, medicos=medicos)

@app.route('/patient/appointments/<int:cita_id>/action', methods=['POST'])
@login_required
def patient_action_cita(cita_id):
    action = request.form.get('action')
    
    # Validar que es 1 dia antes y está aprobada/pendiente
    citas_data, _ = api_get("/citas", params={"id_usuario": session.get('user_id')})
    cita = next((c for c in (citas_data or []) if c.get('id_cita') == cita_id), None)
    
    if not cita:
        flash("❌ Cita no encontrada.")
        return redirect(url_for('patient_appointments'))
        
    if action == 'cancel':
        r = requests.patch(
            f"{API_URL}/citas/{cita_id}/cancelar",
            headers=get_auth_headers(),
            timeout=8
        )
        if r.status_code == 200:
            flash("✅ Cita cancelada con éxito. El médico fue notificado.")
        else:
            try:
                flash(f"❌ Error al cancelar: {r.json().get('detail', 'Error')}")
            except Exception:
                flash("❌ Error al cancelar la cita.")
                
    elif action == 'edit':
        nueva_fecha = request.form.get('nueva_fecha')
        nueva_hora = request.form.get('nueva_hora')
        
        r = requests.patch(
            f"{API_URL}/citas/{cita_id}/reprogramar",
            params={"fecha": nueva_fecha, "hora": nueva_hora},
            headers=get_auth_headers(),
            timeout=8
        )
        if r.status_code == 200:
            flash("✅ Cita reprogramada a estado pendiente. El médico la revisará pronto.")
        else:
            try:
                flash(f"❌ Error al reprogramar: {r.json().get('detail', 'Error')}")
            except Exception:
                flash("❌ Error al reprogramar la cita.")

    return redirect(url_for('patient_appointments'))


@app.route('/patient/articles')
@login_required
def patient_articles():
    articulos, err = api_get("/articulos")
    return render_template('patient/articles.html', articulos=articulos or [])


@app.route('/patient/tips')
@login_required
def patient_tips():
    return render_template('patient/tips.html')


@app.route('/patient/profile', methods=['GET', 'POST'])
@login_required
def patient_profile():
    user_id = session.get('user_id')

    if request.method == 'POST':
        nombre   = request.form.get('nombre', '').strip()
        apellido = request.form.get('apellido_paterno', '').strip()
        telefono = request.form.get('telefono', '').strip()
        foto     = request.files.get('foto')

        try:
            # Construir multipart/form-data para FastAPI
            files = {}
            data  = {}
            if nombre:
                data['nombre'] = nombre
            if apellido:
                data['apellido_paterno'] = apellido
            # Siempre enviar telefono (puede estar vacío para borrarlo)
            data['telefono'] = telefono

            if foto and foto.filename:
                files['foto'] = (foto.filename, foto.stream, foto.mimetype)

            r = requests.patch(
                f"{API_URL}/usuarios/{user_id}",
                data=data,
                files=files if files else None,
                headers=get_auth_headers(),
                timeout=10
            )
            if r.status_code == 200:
                resp = r.json()
                # Actualizar sesión con los nuevos datos para que el sidebar se refresque
                session['nombre']     = f"{resp.get('nombre', '')} {resp.get('apellido_paterno', '')}".strip()
                session['tiene_foto'] = resp.get('tiene_foto', session.get('tiene_foto', False))
                session.modified = True
                flash("✅ Perfil actualizado correctamente.")
            else:
                try:
                    detail = r.json().get('detail', 'Error desconocido.')
                except Exception:
                    detail = r.text
                flash(f"❌ Error al actualizar perfil: {detail}")
        except requests.exceptions.ConnectionError:
            flash("❌ No se pudo conectar con el servidor.")
        except Exception as e:
            flash(f"❌ Error inesperado: {str(e)}")

        return redirect(url_for('patient_profile'))

    # GET — cargar datos frescos del usuario desde la API
    user_info, err = api_get("/usuarios/me")
    if err:
        flash(f"⚠️ No se pudo cargar la información del perfil: {err}")
        user_info = {}
    return render_template('patient/profile.html', user=user_info or {})


@app.route('/patient/avatar')
@login_required
def patient_avatar():
    """Proxy para servir la foto de perfil del usuario desde FastAPI."""
    user_id = session.get('user_id')
    try:
        r = requests.get(
            f"{API_URL}/usuarios/{user_id}/avatar",
            headers=get_auth_headers(),
            timeout=5
        )
        if r.status_code == 200:
            return Response(r.content, content_type=r.headers.get('content-type', 'image/jpeg'))
    except Exception:
        pass
    return '', 404


@app.route('/patient/about')
@login_required

def patient_about():
    return render_template('patient/about.html')


@app.route('/patient/chat')
@login_required
def patient_chat():
    return render_template('patient/chat.html', 
                            user_id=session.get('user_id'), 
                            api_url=API_URL, 
                            token=session.get('token'))

@app.route('/patient/chat/start/<int:id_medico>')
@login_required
def start_patient_chat(id_medico):
    user_id = session.get('user_id')
    payload = {
        "id_usuario": user_id,
        "id_medico": id_medico
    }
    api_post('/chats', json_data=payload)
    return redirect(url_for('patient_chat'))


# ── Doctor Routes ─────────────────────────────────────────────────────────────

@app.route('/doctor/dashboard')
@login_required
def doctor_dashboard():
    total_citas = 0
    pacientes_count = 0
    try:
        user_id = session.get('user_id')
        # Obtener el id_medico del doctor actual
        medicos_data, _ = api_get("/medicos")
        id_medico = None
        if medicos_data:
            for m in medicos_data:
                if m.get('id_usuario') == user_id:
                    id_medico = m.get('id_medico')
                    break

        if id_medico:
            citas_data, _ = api_get("/citas", params={"id_medico": id_medico})
        else:
            citas_data, _ = api_get("/citas")

        if citas_data:
            total_citas = len(citas_data)
            pacientes_ids = set(c.get('id_usuario') for c in citas_data if c.get('id_usuario'))
            pacientes_count = len(pacientes_ids)
    except Exception:
        pass
    return render_template('doctor/dashboard.html', total_citas=total_citas, pacientes=pacientes_count)


@app.route('/doctor/appointments')
@login_required
def doctor_appointments():
    user_id = session.get('user_id')
    # Filtrar citas por el médico actual
    medicos_data, _ = api_get("/medicos")
    id_medico = None
    if medicos_data:
        for m in medicos_data:
            if m.get('id_usuario') == user_id:
                id_medico = m.get('id_medico')
                break

    if id_medico:
        citas, err = api_get("/citas", params={"id_medico": id_medico})
    else:
        citas, err = api_get("/citas")

    if err:
        flash(f"Error al cargar citas: {err}")
        
    usuarios_data, _ = api_get("/usuarios")
    usuarios_map = {u['id_usuario']: f"{u.get('nombre', '')} {u.get('apellido_paterno', '')}" for u in (usuarios_data or [])}
    
    for c in (citas or []):
        c['nombre_paciente'] = usuarios_map.get(c.get('id_usuario'), f"Paciente {c.get('id_usuario')}")
        
    return render_template('doctor/appointments.html', citas=citas or [])


@app.route('/doctor/appointments/<int:cita_id>/estado', methods=['POST'])
@login_required
def doctor_update_cita(cita_id):
    """Permite al médico actualizar el estado de una cita."""
    nuevo_estado = request.form.get('estado', '')
    enlace_meet  = request.form.get('enlace_meet', '')
    mensaje      = request.form.get('mensaje_medico', '')

    params = {"estado": nuevo_estado}
    if enlace_meet:
        params["enlace_meet"] = enlace_meet
    if mensaje:
        params["mensaje_medico"] = mensaje

    try:
        r = requests.patch(
            f"{API_URL}/citas/{cita_id}/estado",
            params=params,
            headers=get_auth_headers(),
            timeout=8
        )
        if r.status_code == 200:
            flash(f"✅ Estado de la cita actualizado a '{nuevo_estado}'.")
        else:
            flash(f"❌ Error al actualizar cita: {r.text}")
    except Exception as e:
        flash(f"❌ Error de conexión: {str(e)}")

    return redirect(url_for('doctor_appointments'))

@app.route('/doctor/appointments/<int:cita_id>/diagnostico', methods=['POST'])
@login_required
def doctor_save_diagnostico(cita_id):
    """Permite al médico guardar un diagnóstico para una cita confirmada."""
    descripcion = request.form.get('descripcion', '')
    if not descripcion:
        flash("❌ El diagnóstico no puede estar vacío.")
        return redirect(url_for('doctor_appointments'))
        
    payload = {
        "id_cita": cita_id,
        "descripcion": descripcion
    }
    
    status_code, resp = api_post("/diagnosticos", json_data=payload)
    if status_code == 200:
        flash("✅ Diagnóstico guardado exitosamente. Podrás verlo y descargarlo en 'Mis Pacientes'.")
    else:
        detail = resp.get('detail', 'Error al guardar el diagnóstico.')
        flash(f"❌ Error: {detail}")

    return redirect(url_for('doctor_appointments'))


@app.route('/doctor/patients')
@login_required
def doctor_patients():
    user_id = session.get('user_id')
    # 1. Obtener id_medico
    medicos_data, _ = api_get("/medicos")
    id_medico = None
    if medicos_data:
        for m in medicos_data:
            if m.get('id_usuario') == user_id:
                id_medico = m.get('id_medico')
                break
                
    # 2. Obtener todas las citas confirmadas de ese médico
    citas_data, _ = api_get("/citas", params={"id_medico": id_medico})
    citas_medico = [c for c in (citas_data or []) if c.get('estado') == 'aprobada']
    
    # 3. Obtener todos los diagnósticos
    diagnosticos_data, _ = api_get("/diagnosticos")
    diagnosticos = diagnosticos_data or []
    
    # 4. Obtener todos los usuarios (pacientes)
    usuarios_data, _ = api_get("/usuarios")
    usuarios_map = {u['id_usuario']: u for u in (usuarios_data or [])}
    
    # Relacionar citas con diagnósticos y agrupar por paciente
    mis_pacientes = {}
    
    for c in citas_medico:
        id_usuario = c.get('id_usuario')
        if not id_usuario: continue
        
        # Buscar diagnósticos para esta cita
        cita_diagnosticos = [d for d in diagnosticos if d.get('id_cita') == c.get('id_cita')]
        
        # Si el usuario no está en el mapa, registrarlo
        if id_usuario not in mis_pacientes:
            u_info = usuarios_map.get(id_usuario, {})
            mis_pacientes[id_usuario] = {
                "id_usuario": id_usuario,
                "nombre_completo": f"{u_info.get('nombre', '')} {u_info.get('apellido_paterno', '')}".strip() or f"Paciente {id_usuario}",
                "citas_con_diagnostico": []
            }
            
        # Añadir al perfil del paciente
        for d in cita_diagnosticos:
            # Agregamos los detalles a la tarjeta del paciente
            mis_pacientes[id_usuario]["citas_con_diagnostico"].append({
                "fecha_cita": c.get("fecha"),
                "motivo": c.get("motivo_cancelacion", "Cita Médica"),
                "descripcion": d.get("descripcion"),
                "fecha_diagnostico": d.get("fecha"),
                "id_diagnostico": d.get("id_diagnostico")
            })

    # Filtrar pacientes que tengan al menos 1 diagnostico o mostrarlos todos? El prompt dice "se podra ver en mis pacientes".
    # Lo más lógico es mostrar los pacientes con citas aprobadas.
    pacientes_list = list(mis_pacientes.values())

    return render_template('doctor/patients.html', pacientes=pacientes_list)


@app.route('/doctor/chat')
@login_required
def doctor_chat():
    user_id = session.get('user_id')
    medicos, _ = api_get("/medicos")
    id_medico = None
    if medicos:
        for m in medicos:
            if m.get('id_usuario') == user_id:
                id_medico = m.get('id_medico')
                break
    return render_template('doctor/chat.html', 
                            user_id=user_id, 
                            id_medico=id_medico, 
                            api_url=API_URL, 
                            token=session.get('token'))

@app.route('/doctor/profile', methods=['GET', 'POST'])
@login_required
def doctor_profile():
    user_id = session.get('user_id')

    if request.method == 'POST':
        telefono = request.form.get('telefono', '').strip()
        foto     = request.files.get('foto')

        try:
            files = {}
            data  = {}
            data['telefono'] = telefono

            if foto and foto.filename:
                files['foto'] = (foto.filename, foto.stream, foto.mimetype)

            r = requests.patch(
                f"{API_URL}/usuarios/{user_id}",
                data=data,
                files=files if files else None,
                headers=get_auth_headers(),
                timeout=10
            )
            if r.status_code == 200:
                resp = r.json()
                # Actualizar sesión con los nuevos datos para que el sidebar se refresque
                session['tiene_foto'] = resp.get('tiene_foto', session.get('tiene_foto', False))
                session.modified = True
                flash("✅ Perfil actualizado correctamente.")
            else:
                try:
                    detail = r.json().get('detail', 'Error desconocido.')
                except Exception:
                    detail = r.text
                flash(f"❌ Error al actualizar perfil: {detail}")
        except requests.exceptions.ConnectionError:
            flash("❌ No se pudo conectar con el servidor.")
        except Exception as e:
            flash(f"❌ Error inesperado: {str(e)}")

        return redirect(url_for('doctor_profile'))

    user_info, err = api_get("/usuarios/me")
    if err:
        flash(f"⚠️ No se pudo cargar la información del perfil: {err}")
        user_info = {}
        
    return render_template('doctor/profile.html', user=user_info or {})

@app.route('/doctor/avatar')
@login_required
def doctor_avatar():
    try:
        user_id = session.get('user_id')
        r = requests.get(
            f"{API_URL}/usuarios/{user_id}/avatar",
            headers=get_auth_headers(),
            timeout=5
        )
        if r.status_code == 200:
            return Response(r.content, content_type=r.headers.get('content-type', 'image/jpeg'))
    except Exception:
        pass
    return '', 404


@app.route('/doctor/content', methods=['GET', 'POST'])
@login_required
def doctor_content():
    if request.method == 'POST':
        titulo    = request.form.get('titulo', '').strip()
        categoria = request.form.get('categoria', '').strip()
        estatus   = 1 if request.form.get('estado') == 'Publicado' else 0
        contenido = request.form.get('contenido', '').strip()

        # Subida de imagen (local)
        imagen = request.files.get('imagen_portada')
        filename = None
        if imagen and imagen.filename:
            filename = imagen.filename
            upload_folder = os.path.join(app.root_path, 'static', 'uploads')
            os.makedirs(upload_folder, exist_ok=True)
            imagen.save(os.path.join(upload_folder, filename))

        # Obtener id_medico del usuario actual
        user_id = session.get('user_id')
        medicos_data, _ = api_get("/medicos")
        id_med = None
        if medicos_data:
            for m in medicos_data:
                if m.get('id_usuario') == user_id:
                    id_med = m.get('id_medico')
                    break
        if not id_med and medicos_data:
            id_med = medicos_data[0].get('id_medico', 1)
        if not id_med:
            id_med = 1

        payload = {
            "id_medico":     id_med,
            "titulo":        titulo,
            "categoria":     categoria,
            "contenido":     contenido,
            "imagen_portada": filename,
            "estatus":       estatus
        }

        status_code, resp = api_post("/articulos", json_data=payload)
        if status_code == 200:
            flash("✅ Artículo guardado correctamente.")
        else:
            flash(f"Error al guardar artículo: {resp.get('detail', 'Intenta de nuevo.')}")

    articulos, err = api_get("/articulos")
    return render_template('doctor/content.html', articulos=articulos or [])


# ── Error Handlers ────────────────────────────────────────────────────────────

@app.errorhandler(404)
def page_not_found(e):
    return render_template('login.html'), 404


# ── Entry Point ───────────────────────────────────────────────────────────────

if __name__ == "__main__":
    os.makedirs(os.path.join(app.root_path, 'static', 'uploads'), exist_ok=True)
    app.run(host="0.0.0.0", port=5000, debug=False)