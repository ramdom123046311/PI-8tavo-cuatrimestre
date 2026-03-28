from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
import sys
import os
from sqlalchemy import text
from fastapi.staticfiles import StaticFiles

sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from app.database import engine, Base, SessionLocal
from app.routes import routes
from app.models import models

import time

# ── Esperar a MySQL con reintentos reales ──────────────────────────────────────
def wait_for_db(retries: int = 20, delay: int = 3):
    """Espera hasta que MySQL acepte conexiones antes de continuar el arranque."""
    for attempt in range(1, retries + 1):
        try:
            with engine.connect() as conn:
                conn.execute(text("SELECT 1"))
            print(f"✅ MySQL listo en el intento {attempt}.")
            return True
        except Exception as e:
            print(f"⏳ Intento {attempt}/{retries} — MySQL no disponible aún: {e}")
            time.sleep(delay)
    print("❌ No se pudo conectar a MySQL. Revisa las variables DB_HOST / DB_PASSWORD.")
    return False

# ── Crear tablas y datos iniciales ─────────────────────────────────────────────
def setup_database():
    """
    Crea las tablas si no existen (create_all es seguro: no borra datos existentes)
    y carga los datos mínimos de arranque (roles + usuarios demo).
    """
    # 1. Crear todas las tablas del modelo si aún no existen
    try:
        Base.metadata.create_all(bind=engine)
        print("✅ Tablas verificadas/creadas correctamente.")
    except Exception as e:
        print(f"⚠️  Error al crear tablas: {e}")

    # 2. Agregar columnas opcionales que pueden faltar en BDs antiguas
    # Usamos try/except individual por columna porque MySQL no soporta
    # "IF NOT EXISTS" en ALTER TABLE via algunos drivers.
    db = SessionLocal()
    try:
        for col_sql in [
            "ALTER TABLE articulos_medicos ADD COLUMN categoria VARCHAR(100)",
            "ALTER TABLE articulos_medicos ADD COLUMN imagen_portada VARCHAR(255)",
            "ALTER TABLE usuarios ADD COLUMN foto_perfil VARCHAR(255)",
            "ALTER TABLE notificaciones ADD COLUMN tipo VARCHAR(50) DEFAULT 'info'",
        ]:
            try:
                db.execute(text(col_sql))
                db.commit()
            except Exception:
                db.rollback()   # La columna ya existe — se ignora el error


        # 3. Roles
        if db.query(models.Rol).count() == 0:
            db.add_all([
                models.Rol(id_rol=1, nombre="usuario"),
                models.Rol(id_rol=2, nombre="medico"),
                models.Rol(id_rol=3, nombre="administrador"),
            ])
            db.commit()
            print("✅ Roles insertados.")

        # 4. Usuarios iniciales con contraseña en TEXTO PLANO (igual que la BD real)
        #    La función verify_password soporta texto plano automáticamente.
        usuarios_demo = [
            {
                "correo": "admin@salud.com",
                "nombre": "Admin", "apellido_paterno": "Principal",
                "genero": "masculino", "fecha_nacimiento": "1990-01-01",
                "telefono": "1234567890", "contrasena": "123456", "id_rol": 3,
            },
            {
                "correo": "maria@gmail.com",
                "nombre": "Maria", "apellido_paterno": "Lopez",
                "genero": "femenino", "fecha_nacimiento": "2000-05-10",
                "telefono": "5551234567", "contrasena": "123456", "id_rol": 1,
            },
            {
                "correo": "medico@gmail.com",
                "nombre": "Juan", "apellido_paterno": "Perez",
                "genero": "masculino", "fecha_nacimiento": "1985-03-20",
                "telefono": "5559876543", "contrasena": "123456", "id_rol": 2,
            },
        ]
        for u in usuarios_demo:
            if db.query(models.Usuario).filter(models.Usuario.correo == u["correo"]).count() == 0:
                db.add(models.Usuario(**u))
                db.commit()
                print(f"✅ Usuario demo creado: {u['correo']}")

        # 5. Registro de medico para Juan Perez (id_usuario = 3)
        juan = db.query(models.Usuario).filter(models.Usuario.correo == "medico@gmail.com").first()
        if juan and db.query(models.Medico).filter(models.Medico.id_usuario == juan.id_usuario).count() == 0:
            db.add(models.Medico(
                id_usuario=juan.id_usuario,
                especialidad="Ginecología",
                descripcion_profesional="Especialista en salud materna",
                anios_experiencia=10,
                aprobado=True,
            ))
            db.commit()
            print("✅ Registro de médico creado para Juan Perez.")

    except Exception as e:
        db.rollback()
        print(f"❌ Error en setup inicial: {e}")
    finally:
        db.close()


# ── Ejecutar setup al iniciar ──────────────────────────────────────────────────
if wait_for_db():
    setup_database()

app = FastAPI(
    title="Salud Materna API",
    description="API central para el sistema de salud materna. Consume esta API desde Flask y Laravel.",
    version="1.0.0"
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

os.makedirs("app/static/uploads", exist_ok=True)
app.mount("/static", StaticFiles(directory="app/static"), name="static")

app.include_router(routes.router, prefix="/api")

@app.get("/api/health")
def health_check():
    """Verifica que la API está corriendo."""
    return {
        "status": "ok",
        "service": "Salud Materna FastAPI",
        "version": "1.0.0"
    }

@app.get("/api/db-test")
def db_test():
    """
    Diagnóstico de conexión a MySQL.
    Visita http://127.0.0.1:8001/api/db-test para verificar la conexión.
    """
    try:
        with engine.connect() as conn:
            result = conn.execute(text("SHOW TABLES"))
            tables = [row[0] for row in result]
        return {"status": "ok", "mensaje": "Conexión a MySQL exitosa", "tablas": tables}
    except Exception as e:
        return {"status": "error", "mensaje": str(e)}
