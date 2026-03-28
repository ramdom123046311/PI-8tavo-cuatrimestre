from fastapi import APIRouter, Depends, HTTPException, status, Query, UploadFile, File, Form
from fastapi.responses import Response, FileResponse
from sqlalchemy.orm import Session
from typing import List, Optional
from app.database import get_db
from app.models import models
from app.schemas import schemas
from app.services.auth import verify_password, get_password_hash, create_access_token, get_current_user
from datetime import datetime, timedelta
import os
import time


router = APIRouter()

# ── AUTH & USERS ───────────────────────────────────────────────────────────────

@router.post("/login", response_model=schemas.Token)
def login_for_access_token(user_data: schemas.LoginSchema, db: Session = Depends(get_db)):
    """Autentica al usuario y devuelve token JWT + datos básicos del usuario."""
    user = db.query(models.Usuario).filter(models.Usuario.correo == user_data.correo).first()

    if not user:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Correo o contraseña incorrectos",
            headers={"WWW-Authenticate": "Bearer"},
        )

    if not verify_password(user_data.contrasena, user.contrasena):
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Correo o contraseña incorrectos",
            headers={"WWW-Authenticate": "Bearer"},
        )

    if user.estatus == 0:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="User account is inactive"
        )

    access_token = create_access_token(data={"sub": user.correo})
    return {
        "access_token": access_token,
        "token_type": "bearer",
        "user": {
            "id":     user.id_usuario,
            "rol":    user.id_rol,
            "nombre": user.nombre,
            "apellido": user.apellido_paterno,
            "tiene_foto": user.foto_perfil is not None
        }
    }


@router.get("/usuarios/me")
def read_users_me(current_user: models.Usuario = Depends(get_current_user)):
    """Devuelve la información completa del usuario autenticado actualmente."""
    return {
        "id_usuario":        current_user.id_usuario,
        "nombre":            current_user.nombre,
        "apellido_paterno":  current_user.apellido_paterno,
        "genero":            current_user.genero,
        "fecha_nacimiento":  str(current_user.fecha_nacimiento),
        "telefono":          current_user.telefono or "",
        "correo":            current_user.correo,
        "id_rol":            current_user.id_rol,
        "estatus":           current_user.estatus,
        "foto_perfil":       current_user.foto_perfil,
        "tiene_foto":        current_user.foto_perfil is not None,
        "fecha_registro":    str(current_user.fecha_registro),
    }


@router.get("/usuarios", response_model=List[schemas.UsuarioResponse])
def get_usuarios(db: Session = Depends(get_db)):
    """Lista todos los usuarios que no han sido eliminados (soft delete)."""
    return db.query(models.Usuario).filter(models.Usuario.deleted_at == None).all()


@router.post("/usuarios", response_model=schemas.UsuarioResponse)
def create_usuario(usuario: schemas.UsuarioCreate, db: Session = Depends(get_db)):
    """Registra un nuevo usuario hasheando su contraseña."""
    db_user = db.query(models.Usuario).filter(models.Usuario.correo == usuario.correo).first()
    if db_user:
        raise HTTPException(status_code=400, detail="Este correo ya está registrado")

    hashed_password = get_password_hash(usuario.contrasena)
    dict_user = usuario.dict(exclude={"cp", "especialidad", "anios_experiencia"})
    dict_user["contrasena"] = hashed_password
    
    if dict_user["id_rol"] == 2:
        dict_user["estatus"] = 0
    else:
        dict_user["estatus"] = 1

    db_usuario = models.Usuario(**dict_user)
    db.add(db_usuario)
    db.commit()
    db.refresh(db_usuario)
    
    if dict_user["id_rol"] == 2:
        db_medico = models.Medico(
            id_usuario=db_usuario.id_usuario,
            cp=usuario.cp,
            especialidad=usuario.especialidad,
            descripcion_profesional=None,
            anios_experiencia=usuario.anios_experiencia,
            estatus='suspendido',
            aprobado=False
        )
        db.add(db_medico)
        db.commit()

    return db_usuario


@router.patch("/usuarios/{id}")
def update_usuario(
    id: int,
    nombre: Optional[str] = Form(None),
    apellido_paterno: Optional[str] = Form(None),
    telefono: Optional[str] = Form(None),
    estatus: Optional[int] = Form(None),
    foto: Optional[UploadFile] = File(None),
    db: Session = Depends(get_db)
):
    """Actualiza perfil del usuario: nombre, apellido, teléfono, estatus y foto.
    Los campos correo, genero y fecha_nacimiento NO son modificables.
    """
    user = db.query(models.Usuario).filter(models.Usuario.id_usuario == id).first()
    if not user:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")

    # Solo actualizar campos que vienen con valor
    if nombre is not None and nombre.strip():
        user.nombre = nombre.strip()
    if apellido_paterno is not None and apellido_paterno.strip():
        user.apellido_paterno = apellido_paterno.strip()
    if telefono is not None:
        user.telefono = telefono.strip() if telefono.strip() else None
    if estatus is not None:
        user.estatus = estatus

    if foto and foto.filename:
        ext = foto.filename.rsplit('.', 1)[-1].lower()
        if ext not in ["jpg", "jpeg", "png", "webp"]:
            raise HTTPException(status_code=400, detail="Formato no soportado. Usa JPG, PNG o WEBP.")

        imagen_bytes = foto.file.read()
        if len(imagen_bytes) > 5 * 1024 * 1024:   # 5 MB máx
            raise HTTPException(status_code=400, detail="La imagen supera los 5MB permitidos.")

        os.makedirs("app/static/uploads", exist_ok=True)
        filename = f"avatar_{id}_{int(time.time())}.{ext}"
        filepath = os.path.join("app/static/uploads", filename)

        # Eliminar foto anterior si existe
        if user.foto_perfil:
            old_path = "app" + user.foto_perfil
            if os.path.exists(old_path):
                try:
                    os.remove(old_path)
                except Exception:
                    pass

        with open(filepath, "wb") as f:
            f.write(imagen_bytes)

        user.foto_perfil = f"/static/uploads/{filename}"

    db.commit()
    db.refresh(user)

    return {
        "msg": "Perfil actualizado correctamente",
        "id_usuario":       user.id_usuario,
        "nombre":           user.nombre,
        "apellido_paterno": user.apellido_paterno,
        "telefono":         user.telefono or "",
        "correo":           user.correo,
        "genero":           user.genero,
        "fecha_nacimiento": str(user.fecha_nacimiento),
        "foto_perfil":      user.foto_perfil,
        "tiene_foto":       user.foto_perfil is not None,
    }


@router.delete("/usuarios/{id}")
def delete_usuario(id: int, db: Session = Depends(get_db)):
    """Realiza soft delete de un usuario (marca como eliminado sin borrar datos)."""
    db_user = db.query(models.Usuario).filter(models.Usuario.id_usuario == id).first()
    if not db_user:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")
    db_user.deleted_at = datetime.utcnow()
    db.commit()
    return {"msg": "Usuario eliminado correctamente"}



@router.get("/usuarios/{id}/avatar")
def get_avatar(id: int, db: Session = Depends(get_db)):
    """Devuelve la imagen de perfil guardada en el directorio local."""
    user = db.query(models.Usuario).filter(models.Usuario.id_usuario == id).first()
    if not user or not user.foto_perfil:
        raise HTTPException(status_code=404, detail="Sin foto de perfil")

    filepath = "app" + user.foto_perfil
    if not os.path.exists(filepath):
        raise HTTPException(status_code=404, detail="Archivo de foto no encontrado")

    return FileResponse(filepath)


# ── MEDICOS ────────────────────────────────────────────────────────────────────

@router.get("/medicos", response_model=List[schemas.MedicoResponse])
def get_medicos(db: Session = Depends(get_db)):
    return db.query(models.Medico).all()


@router.get("/medicos/con-nombre")
def get_medicos_con_nombre(
    solo_activos: bool = Query(False, description="Filtrar médicos aprobados y usuarios activos"),
    db: Session = Depends(get_db)
):
    """Devuelve lista de médicos con nombre y apellido del usuario asociado."""
    medicos = db.query(models.Medico).all()
    resultado = []
    for m in medicos:
        usuario = db.query(models.Usuario).filter(models.Usuario.id_usuario == m.id_usuario).first()
        
        if solo_activos:
            if not usuario or usuario.estatus == 0 or not m.aprobado:
                continue
                
        nombre_completo = f"{usuario.nombre} {usuario.apellido_paterno}" if usuario else f"Médico {m.id_medico}"
        resultado.append({
            "id_medico": m.id_medico,
            "id_usuario": m.id_usuario,
            "nombre_completo": nombre_completo,
            "cp": m.cp,
            "especialidad": m.especialidad or "Médico General",
            "descripcion_profesional": m.descripcion_profesional or "",
            "anios_experiencia": m.anios_experiencia or 0,
            "estatus": m.estatus,
            "aprobado": m.aprobado,
        })
    return resultado


@router.post("/medicos", response_model=schemas.MedicoResponse)
def create_medico(medico: schemas.MedicoCreate, db: Session = Depends(get_db)):
    db_medico = models.Medico(**medico.dict())
    db.add(db_medico)
    db.commit()
    db.refresh(db_medico)
    return db_medico


@router.patch("/medicos/{id}")
def update_medico(id: int, updates: dict, db: Session = Depends(get_db)):
    db.query(models.Medico).filter(models.Medico.id_medico == id).update(updates)
    db.commit()
    return {"msg": "Medico actualizado"}


@router.delete("/medicos/{id}")
def delete_medico(id: int, db: Session = Depends(get_db)):
    db_med = db.query(models.Medico).filter(models.Medico.id_medico == id).first()
    if not db_med:
        raise HTTPException(status_code=404, detail="Medico no encontrado")
    db.delete(db_med)
    db.commit()
    return {"msg": "Medico eliminado"}


# ── CITAS ──────────────────────────────────────────────────────────────────────

def _serialize_cita(cita: models.Cita) -> dict:
    """Serializa una cita convirtiendo time y date a string."""
    hora_str = None
    if cita.hora is not None:
        # hora puede ser timedelta (MySQL) o time
        if hasattr(cita.hora, 'seconds'):
            total = int(cita.hora.total_seconds())
            h, remainder = divmod(total, 3600)
            m, s = divmod(remainder, 60)
            hora_str = f"{h:02d}:{m:02d}:{s:02d}"
        else:
            hora_str = str(cita.hora)

    return {
        "id_cita":            cita.id_cita,
        "id_usuario":         cita.id_usuario,
        "id_medico":          cita.id_medico,
        "fecha":              str(cita.fecha),
        "hora":               hora_str,
        "estado":             cita.estado,
        "motivo_consulta":    cita.motivo_cancelacion,   # campo reutilizado para motivo
        "motivo_cancelacion": cita.motivo_cancelacion,
        "enlace_meet":        cita.enlace_meet,
        "mensaje_medico":     cita.mensaje_medico,
        "created_at":         str(cita.created_at),
    }


def _serialize_cita_admin(cita: models.Cita, db: Session = None) -> dict:
    """Serializa una cita con información de usuario, médico y datos completos (para admin)."""
    hora_str = None
    if cita.hora is not None:
        if hasattr(cita.hora, 'seconds'):
            total = int(cita.hora.total_seconds())
            h, remainder = divmod(total, 3600)
            m, s = divmod(remainder, 60)
            hora_str = f"{h:02d}:{m:02d}:{s:02d}"
        else:
            hora_str = str(cita.hora)
    
    # Obtener datos del usuario y médico (pasaremos db si lo necesitamos)
    user_info = {"id": cita.id_usuario, "nombre": "Usuario", "correo": ""}
    medico_info = {"id": cita.id_medico, "nombre": "Médico", "especialidad": ""}
    
    return {
        "id_cita":            cita.id_cita,
        "id_usuario":         cita.id_usuario,
        "id_medico":          cita.id_medico,
        "fecha":              str(cita.fecha),
        "hora":               hora_str,
        "estado":             cita.estado,
        "motivo_cancelacion": cita.motivo_cancelacion or "",
        "enlace_meet":        cita.enlace_meet,
        "mensaje_medico":     cita.mensaje_medico,
        "created_at":         str(cita.created_at),
        "deleted_at":         str(cita.deleted_at) if cita.deleted_at else None,
        "is_deleted":         cita.deleted_at is not None
    }


@router.get("/citas")
def get_citas(
    id_usuario: Optional[int] = Query(None, description="Filtrar por paciente"),
    id_medico: Optional[int] = Query(None, description="Filtrar por médico"),
    db: Session = Depends(get_db)
):
    """Devuelve citas no eliminadas. Opcionalmente filtradas por paciente o médico."""
    query = db.query(models.Cita).filter(models.Cita.deleted_at == None)
    if id_usuario is not None:
        query = query.filter(models.Cita.id_usuario == id_usuario)
    if id_medico is not None:
        query = query.filter(models.Cita.id_medico == id_medico)
    citas = query.order_by(models.Cita.fecha.desc(), models.Cita.hora.desc()).all()
    return [_serialize_cita(c) for c in citas]


@router.get("/admin/citas")
def get_admin_citas(db: Session = Depends(get_db)):
    """Devuelve TODAS las citas (incluyendo canceladas) para administrador."""
    citas = db.query(models.Cita).order_by(models.Cita.fecha.desc(), models.Cita.hora.desc()).all()
    return [_serialize_cita_admin(c) for c in citas]


@router.post("/citas")
def create_cita(cita: schemas.CitaCreate, db: Session = Depends(get_db)):
    """Crea una nueva cita médica y la guarda en la base de datos."""
    # Verificar que el médico existe
    medico = db.query(models.Medico).filter(models.Medico.id_medico == cita.id_medico).first()
    if not medico:
        raise HTTPException(status_code=404, detail="Médico no encontrado")

    # Verificar que el usuario existe
    usuario = db.query(models.Usuario).filter(models.Usuario.id_usuario == cita.id_usuario).first()
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")

    db_cita = models.Cita(
        id_usuario=cita.id_usuario,
        id_medico=cita.id_medico,
        fecha=cita.fecha,
        hora=cita.hora,
        estado="pendiente",
        motivo_cancelacion=cita.motivo_cancelacion,   # se reutiliza para guardar motivo de consulta
    )
    db.add(db_cita)
    db.commit()
    db.refresh(db_cita)
    return _serialize_cita(db_cita)


@router.patch("/citas/{id}/estado")
def update_cita_estado(
    id: int,
    estado: str = Query(..., description="Nuevo estado: pendiente|aprobada|rechazada|cancelada"),
    enlace_meet: Optional[str] = Query(None),
    mensaje_medico: Optional[str] = Query(None),
    db: Session = Depends(get_db)
):
    """Actualiza el estado de una cita. Si se aprueba, puede incluir enlace de Meet."""
    estados_validos = {"pendiente", "aprobada", "rechazada", "cancelada"}
    if estado not in estados_validos:
        raise HTTPException(status_code=400, detail=f"Estado inválido. Usa: {estados_validos}")

    cita = db.query(models.Cita).filter(models.Cita.id_cita == id).first()
    if not cita:
        raise HTTPException(status_code=404, detail="Cita no encontrada")

    cita.estado = estado
    if enlace_meet is not None:
        cita.enlace_meet = enlace_meet
    if mensaje_medico is not None:
        cita.mensaje_medico = mensaje_medico

    db.commit()
    db.refresh(cita)

    # Extra logic: Si el medico envía una contrapropuesta (rechazada + mensaje), enviar al chat
    if estado == "rechazada" and mensaje_medico is not None and mensaje_medico.strip() != "":
        chat = db.query(models.Chat).filter(
            models.Chat.id_usuario == cita.id_usuario,
            models.Chat.id_medico == cita.id_medico
        ).first()

        if not chat:
            chat = models.Chat(id_usuario=cita.id_usuario, id_medico=cita.id_medico)
            db.add(chat)
            db.commit()
            db.refresh(chat)

        if chat.estatus == 'activo':
            msg_obj = models.Mensaje(
                id_chat=chat.id_chat,
                emisor="medico",
                mensaje=f"Contrapropuesta de Cita:\n{mensaje_medico.strip()}"
            )
            db.add(msg_obj)
            
            # Notification for the patient
            med = db.query(models.Medico).filter(models.Medico.id_medico == cita.id_medico).first()
            if med:
                u_med = db.query(models.Usuario).filter(models.Usuario.id_usuario == med.id_usuario).first()
                if u_med:
                    notif = models.Notificacion(
                        id_usuario=cita.id_usuario,
                        mensaje=f"Tienes un nuevo mensaje del Dr. {u_med.nombre} {u_med.apellido_paterno}",
                        tipo='info'
                    )
                    db.add(notif)
                    
            db.commit()

    return _serialize_cita(cita)

@router.patch("/citas/{id}/reprogramar")
def reprogramar_cita(
    id: int,
    fecha: str = Query(..., description="Nueva fecha YYYY-MM-DD"),
    hora: str = Query(..., description="Nueva hora HH:MM:SS"),
    db: Session = Depends(get_db)
):
    cita = db.query(models.Cita).filter(models.Cita.id_cita == id).first()
    if not cita:
        raise HTTPException(status_code=404, detail="Cita no encontrada")
    
    hoy = datetime.now().date()
    # Validar que la cita actual sea modificable: debe estar a 2+ días de distancia
    if (cita.fecha - hoy) < timedelta(days=2):
        raise HTTPException(status_code=400, detail="No puedes reprogramar con menos de 2 días de anticipación.")
    
    cita.fecha = datetime.strptime(fecha, "%Y-%m-%d").date()
    cita.hora = datetime.strptime(hora, "%H:%M:%S" if len(hora) == 8 else "%H:%M").time()
    cita.estado = "pendiente"

    medico = db.query(models.Medico).filter(models.Medico.id_medico == cita.id_medico).first()
    if medico:
        notif = models.Notificacion(
            id_usuario=medico.id_usuario,
            mensaje=f"El paciente ha solicitado reprogramar la cita para el {fecha} a las {hora}. Revisa tus solicitudes.",
            tipo='info'
        )
        db.add(notif)
    db.commit()
    db.refresh(cita)
    return _serialize_cita(cita)

@router.patch("/citas/{id}/cancelar")
def cancelar_cita(id: int, db: Session = Depends(get_db)):
    cita = db.query(models.Cita).filter(models.Cita.id_cita == id).first()
    if not cita:
        raise HTTPException(status_code=404, detail="Cita no encontrada")

    hoy = datetime.now().date()
    if (cita.fecha - hoy) <= timedelta(days=1):
        raise HTTPException(status_code=400, detail="No puedes cancelar con menos de un día de anticipación.")
        
    cita.estado = "cancelada"
    medico = db.query(models.Medico).filter(models.Medico.id_medico == cita.id_medico).first()
    if medico:
        notif = models.Notificacion(
            id_usuario=medico.id_usuario,
            mensaje=f"El paciente ha cancelado su cita del {cita.fecha}.",
            tipo='alerta'
        )
        db.add(notif)
    db.commit()
    return {"msg": "Cita cancelada correctamente"}



@router.delete("/citas/{id}")
def delete_cita(id: int, db: Session = Depends(get_db)):
    """Realiza soft delete de una cita (para usuarios normales)."""
    cita = db.query(models.Cita).filter(models.Cita.id_cita == id).first()
    if not cita:
        raise HTTPException(status_code=404, detail="Cita no encontrada")
    cita.deleted_at = datetime.utcnow()
    db.commit()
    return {"msg": "Cita cancelada correctamente"}


# ── ADMIN CITAS ────────────────────────────────────────────────────────────────

@router.post("/admin/citas")
def admin_create_cita(
    id_usuario: int = Query(...),
    id_medico: int = Query(...),
    fecha: str = Query(..., description="YYYY-MM-DD"),
    hora: str = Query(..., description="HH:MM:SS o HH:MM"),
    motivo_cancelacion: Optional[str] = Query(None),
    db: Session = Depends(get_db)
):
    """Admin crea una cita. Sin validación de días (admin puede crear citas en cualquier momento)."""
    usuario = db.query(models.Usuario).filter(models.Usuario.id_usuario == id_usuario).first()
    if not usuario:
        raise HTTPException(status_code=404, detail="Paciente no encontrado")
    
    medico = db.query(models.Medico).filter(models.Medico.id_medico == id_medico).first()
    if not medico:
        raise HTTPException(status_code=404, detail="Médico no encontrado")
    
    cita_fecha = datetime.strptime(fecha, "%Y-%m-%d").date()
    cita_hora = datetime.strptime(hora, "%H:%M:%S" if len(hora) == 8 else "%H:%M").time()
    
    db_cita = models.Cita(
        id_usuario=id_usuario,
        id_medico=id_medico,
        fecha=cita_fecha,
        hora=cita_hora,
        estado="aprobada",  # Admin crea directamente aprobada
        motivo_cancelacion=motivo_cancelacion
    )
    db.add(db_cita)
    db.commit()
    db.refresh(db_cita)
    
    # Notificaciones
    notif_usuario = models.Notificacion(
        id_usuario=id_usuario,
        mensaje=f"El administrador ha creado una cita para el {fecha} a las {hora}",
        tipo='info'
    )
    notif_medico = models.Notificacion(
        id_usuario=medico.id_usuario,
        mensaje=f"El administrador ha creado una cita para el {fecha} a las {hora}",
        tipo='info'
    )
    db.add(notif_usuario)
    db.add(notif_medico)
    db.commit()
    
    return _serialize_cita_admin(db_cita)


@router.patch("/admin/citas/{id}/edit")
def admin_edit_cita(
    id: int,
    fecha: Optional[str] = Query(None, description="YYYY-MM-DD"),
    hora: Optional[str] = Query(None, description="HH:MM:SS o HH:MM"),
    estado: Optional[str] = Query(None),
    db: Session = Depends(get_db)
):
    """Admin edita fecha/hora/estado de una cita y notifica a ambos."""
    cita = db.query(models.Cita).filter(models.Cita.id_cita == id).first()
    if not cita:
        raise HTTPException(status_code=404, detail="Cita no encontrada")
    
    cambios = []
    
    if fecha:
        cita_fecha = datetime.strptime(fecha, "%Y-%m-%d").date()
        cambios.append(f"fecha a {fecha}")
        cita.fecha = cita_fecha
    
    if hora:
        cita_hora = datetime.strptime(hora, "%H:%M:%S" if len(hora) == 8 else "%H:%M").time()
        cambios.append(f"hora a {hora}")
        cita.hora = cita_hora
    
    if estado and estado in {"pendiente", "aprobada", "rechazada", "cancelada"}:
        cambios.append(f"estado a {estado}")
        cita.estado = estado
    
    db.commit()
    db.refresh(cita)
    
    # Notificaciones
    cambios_str = ", ".join(cambios)
    notif_usuario = models.Notificacion(
        id_usuario=cita.id_usuario,
        mensaje=f"El administrador ha editado tu cita: {cambios_str}",
        tipo='alerta'
    )
    medico = db.query(models.Medico).filter(models.Medico.id_medico == cita.id_medico).first()
    if medico:
        notif_medico = models.Notificacion(
            id_usuario=medico.id_usuario,
            mensaje=f"El administrador ha editado una cita: {cambios_str}",
            tipo='alerta'
        )
        db.add(notif_medico)
    
    db.add(notif_usuario)
    db.commit()
    
    return _serialize_cita_admin(cita)


@router.patch("/admin/citas/{id}/cancelar")
def admin_cancel_cita(id: int, db: Session = Depends(get_db)):
    """Admin cancela una cita y notifica a paciente y médico."""
    cita = db.query(models.Cita).filter(models.Cita.id_cita == id).first()
    if not cita:
        raise HTTPException(status_code=404, detail="Cita no encontrada")
    
    cita.estado = "cancelada"
    db.commit()
    db.refresh(cita)
    
    # Notificaciones
    notif_usuario = models.Notificacion(
        id_usuario=cita.id_usuario,
        mensaje=f"El administrador ha cancelado tu cita del {cita.fecha}",
        tipo='alerta'
    )
    medico = db.query(models.Medico).filter(models.Medico.id_medico == cita.id_medico).first()
    if medico:
        notif_medico = models.Notificacion(
            id_usuario=medico.id_usuario,
            mensaje=f"El administrador ha cancelado una cita del {cita.fecha}",
            tipo='alerta'
        )
        db.add(notif_medico)
    
    db.add(notif_usuario)
    db.commit()
    
    return {"msg": "Cita cancelada correctamente"}


@router.delete("/admin/citas/{id}")
def admin_delete_cita(id: int, db: Session = Depends(get_db)):
    """Admin elimina (soft delete) una cita y notifica a paciente y médico."""
    cita = db.query(models.Cita).filter(models.Cita.id_cita == id).first()
    if not cita:
        raise HTTPException(status_code=404, detail="Cita no encontrada")
    
    cita.deleted_at = datetime.utcnow()
    db.commit()
    
    # Notificaciones
    notif_usuario = models.Notificacion(
        id_usuario=cita.id_usuario,
        mensaje=f"El administrador ha eliminado tu cita del {cita.fecha}",
        tipo='alerta'
    )
    medico = db.query(models.Medico).filter(models.Medico.id_medico == cita.id_medico).first()
    if medico:
        notif_medico = models.Notificacion(
            id_usuario=medico.id_usuario,
            mensaje=f"El administrador ha eliminado una cita del {cita.fecha}",
            tipo='alerta'
        )
        db.add(notif_medico)
    
    db.add(notif_usuario)
    db.commit()
    
    return {"msg": "Cita eliminada correctamente"}


# ── ARTICULOS ──────────────────────────────────────────────────────────────────

@router.get("/articulos", response_model=List[schemas.ArticuloResponse])
def get_articulos(db: Session = Depends(get_db)):
    return db.query(models.ArticuloMedico).all()


@router.post("/articulos", response_model=schemas.ArticuloResponse)
def create_articulo(articulo: schemas.ArticuloCreate, db: Session = Depends(get_db)):
    db_art = models.ArticuloMedico(**articulo.dict())
    db.add(db_art)
    db.commit()
    db.refresh(db_art)
    return db_art


@router.delete("/articulos/{id}")
def delete_articulo(id: int, db: Session = Depends(get_db)):
    db.query(models.ArticuloMedico).filter(models.ArticuloMedico.id_articulo == id).delete()
    db.commit()
    return {"msg": "Eliminado correctamente"}


# ── NOTIFICACIONES ─────────────────────────────────────────────────────────────

@router.get("/notificaciones/{id_usuario}", response_model=List[schemas.NotificacionResponse])
def get_notifs(id_usuario: int, db: Session = Depends(get_db)):
    return db.query(models.Notificacion).filter(models.Notificacion.id_usuario == id_usuario).all()


@router.post("/notificaciones", response_model=schemas.NotificacionResponse)
def create_notif(notif: schemas.NotificacionCreate, db: Session = Depends(get_db)):
    db_notif = models.Notificacion(**notif.dict())
    db.add(db_notif)
    db.commit()
    db.refresh(db_notif)
    return db_notif

@router.patch("/notificaciones/{id_notificacion}/leer")
def read_notif(id_notificacion: int, db: Session = Depends(get_db)):
    notif = db.query(models.Notificacion).filter(models.Notificacion.id_notificacion == id_notificacion).first()
    if not notif:
        raise HTTPException(status_code=404, detail="Notificación no encontrada")
    notif.leido = True
    db.commit()
    return {"msg": "Notificación leída"}

@router.post("/reportes")
def reportar_usuario(
    reportado_id: int = Query(...),
    motivo: str = Query(...),
    detalles: Optional[str] = Query(None),
    db: Session = Depends(get_db)
):
    admin = db.query(models.Usuario).filter(models.Usuario.id_rol == 3).first()
    if not admin:
        return {"msg": "Operación simulada (sin admin)"}
        
    tipo = 'peligro' if motivo == 'Amenaza' else 'alerta'
    mensaje_final = f"Reporte a usuario ID {reportado_id}. Motivo: {motivo}."
    if detalles:
        mensaje_final += f" Detalles: {detalles}"
        
    notif = models.Notificacion(
        id_usuario=admin.id_usuario,
        mensaje=mensaje_final,
        tipo=tipo
    )
    db.add(notif)
    db.commit()
    return {"msg": "Reporte enviado al administrador"}

# ── DIAGNOSTICOS ───────────────────────────────────────────────────────────────

@router.get("/diagnosticos", response_model=List[schemas.DiagnosticoResponse])
def get_diagnosticos(id_cita: Optional[int] = Query(None), db: Session = Depends(get_db)):
    query = db.query(models.Diagnostico)
    if id_cita:
        query = query.filter(models.Diagnostico.id_cita == id_cita)
    return query.all()

@router.post("/diagnosticos", response_model=schemas.DiagnosticoResponse)
def create_diagnostico(diagnostico: schemas.DiagnosticoCreate, db: Session = Depends(get_db)):
    db_diag = models.Diagnostico(**diagnostico.dict())
    db.add(db_diag)
    db.commit()
    db.refresh(db_diag)
    return db_diag

# ── CHATS & MENSAJES ───────────────────────────────────────────────────────────

@router.get("/chats", response_model=List[schemas.ChatResponse])
def get_all_chats(db: Session = Depends(get_db)):
    """Admin: obtener todos los chats."""
    return db.query(models.Chat).all()

@router.get("/chats/usuario/{id_usuario}", response_model=List[schemas.ChatResponse])
def get_chats_usuario(id_usuario: int, db: Session = Depends(get_db)):
    return db.query(models.Chat).filter(models.Chat.id_usuario == id_usuario).all()

@router.get("/chats/medico/{id_medico}", response_model=List[schemas.ChatResponse])
def get_chats_medico(id_medico: int, db: Session = Depends(get_db)):
    return db.query(models.Chat).filter(models.Chat.id_medico == id_medico).all()

@router.post("/chats", response_model=schemas.ChatResponse)
def create_chat(chat: schemas.ChatCreate, db: Session = Depends(get_db)):
    # Check if chat already exists
    existing_chat = db.query(models.Chat).filter(
        models.Chat.id_usuario == chat.id_usuario,
        models.Chat.id_medico == chat.id_medico
    ).first()
    if existing_chat:
        return existing_chat

    db_chat = models.Chat(id_usuario=chat.id_usuario, id_medico=chat.id_medico)
    db.add(db_chat)
    db.commit()
    db.refresh(db_chat)
    return db_chat

@router.patch("/chats/{id_chat}/estatus")
def update_chat_estatus(id_chat: int, estatus: str = Query(..., description="'activo' o 'restringido'"), db: Session = Depends(get_db)):
    chat = db.query(models.Chat).filter(models.Chat.id_chat == id_chat).first()
    if not chat:
        raise HTTPException(status_code=404, detail="Chat no encontrado")
    if estatus not in ['activo', 'restringido']:
        raise HTTPException(status_code=400, detail="Estatus inválido")
    chat.estatus = estatus
    db.commit()
    return {"msg": f"Chat {estatus}"}

@router.get("/chats/{id_chat}/mensajes", response_model=List[schemas.MensajeResponse])
def get_mensajes(id_chat: int, db: Session = Depends(get_db)):
    return db.query(models.Mensaje).filter(models.Mensaje.id_chat == id_chat).order_by(models.Mensaje.fecha_envio.asc()).all()

@router.post("/mensajes", response_model=schemas.MensajeResponse)
def create_mensaje(mensaje: schemas.MensajeCreate, db: Session = Depends(get_db)):
    # Verifica que el chat exista y esté activo
    chat = db.query(models.Chat).filter(models.Chat.id_chat == mensaje.id_chat).first()
    if not chat:
        raise HTTPException(status_code=404, detail="Chat no encontrado")
    if chat.estatus == 'restringido':
        raise HTTPException(status_code=403, detail="Este chat ha sido restringido por un administrador")

    db_mensaje = models.Mensaje(
        id_chat=mensaje.id_chat,
        emisor=mensaje.emisor,
        mensaje=mensaje.mensaje
    )
    db.add(db_mensaje)
    db.commit()
    db.refresh(db_mensaje)

    # Crear notificacion para el receptor
    receptor_id = chat.id_usuario if mensaje.emisor == 'medico' else db.query(models.Medico).filter(models.Medico.id_medico == chat.id_medico).first().id_usuario
    emisor_nombre = "el usuario" if mensaje.emisor == 'usuario' else "el médico"

    # Buscar nombre del emisor
    if mensaje.emisor == 'usuario':
        u = db.query(models.Usuario).filter(models.Usuario.id_usuario == chat.id_usuario).first()
        emisor_nombre = f"{u.nombre} {u.apellido_paterno}" if u else "un paciente"
    else:
        m = db.query(models.Medico).filter(models.Medico.id_medico == chat.id_medico).first()
        if m:
            u = db.query(models.Usuario).filter(models.Usuario.id_usuario == m.id_usuario).first()
            if u:
                emisor_nombre = f"el Dr. {u.nombre} {u.apellido_paterno}"

    notif = models.Notificacion(
        id_usuario=receptor_id,
        mensaje=f"Tienes un nuevo mensaje de {emisor_nombre}"
    )
    db.add(notif)
    db.commit()

    return db_mensaje