import os
from sqlalchemy import create_engine
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker

DB_HOST     = os.getenv("DB_HOST",     "127.0.0.1")
DB_USER     = os.getenv("DB_USER",     "root")
DB_PASSWORD = os.getenv("DB_PASSWORD", "2983")      # ← Cambia esto o usa variable de entorno
DB_NAME     = os.getenv("DB_NAME",     "salud_maternal")
DB_PORT     = os.getenv("DB_PORT",     "3306")

# charset=utf8mb4 evita errores con caracteres especiales y emojis
SQLALCHEMY_DATABASE_URL = (
    f"mysql+pymysql://{DB_USER}:{DB_PASSWORD}"
    f"@{DB_HOST}:{DB_PORT}/{DB_NAME}?charset=utf8mb4"
)

engine = create_engine(
    SQLALCHEMY_DATABASE_URL,
    pool_pre_ping=True,   # detecta conexiones muertas automáticamente
    pool_recycle=3600,    # recicla conexiones cada hora
    echo=True,            # ← DEBUG: muestra cada SQL en consola (ponlo en False cuando termines)
)
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)

Base = declarative_base()

def get_db():
    """Generador de sesiones de BD para inyección de dependencias en FastAPI."""
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()