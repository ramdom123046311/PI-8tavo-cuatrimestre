from sqlalchemy import Column, Integer, String, Boolean, Date, Time, Enum, ForeignKey, Text, TIMESTAMP, LargeBinary, DateTime
from sqlalchemy.orm import relationship
from datetime import datetime
from app.database import Base

class Rol(Base):
    __tablename__ = "roles"
    id_rol = Column(Integer, primary_key=True, index=True, autoincrement=True)
    nombre = Column(String(50), unique=True, nullable=False)

class Usuario(Base):
    __tablename__ = "usuarios"
    id_usuario = Column(Integer, primary_key=True, index=True, autoincrement=True)
    nombre = Column(String(100), nullable=False)
    apellido_paterno = Column(String(100), nullable=False)
    genero = Column(Enum('masculino', 'femenino'), nullable=False)
    fecha_nacimiento = Column(Date, nullable=False)
    telefono = Column(String(15))
    correo = Column(String(150), unique=True, nullable=False)
    contrasena = Column(String(255), nullable=False)
    id_rol = Column(Integer, ForeignKey("roles.id_rol"), nullable=False)
    estatus = Column(Integer, default=1)
    fecha_registro = Column(TIMESTAMP, default=datetime.utcnow)
    foto_perfil = Column(String(255), nullable=True)   # varchar(255) — ruta de imagen guardada en BD
    deleted_at = Column(DateTime, nullable=True)  # soft delete timestamp

    
    medico = relationship("Medico", back_populates="usuario", uselist=False)

class Medico(Base):
    __tablename__ = "medicos"
    id_medico = Column(Integer, primary_key=True, index=True, autoincrement=True)
    id_usuario = Column(Integer, ForeignKey("usuarios.id_usuario"), unique=True)
    cp = Column(String(11))
    especialidad = Column(String(100))
    descripcion_profesional = Column(Text)
    anios_experiencia = Column(Integer)
    estatus = Column(Enum('activo', 'suspendido'), default='activo')
    aprobado = Column(Boolean, default=False)
    
    usuario = relationship("Usuario", back_populates="medico")

class Cita(Base):
    __tablename__ = "citas"
    id_cita = Column(Integer, primary_key=True, index=True, autoincrement=True)
    id_usuario = Column(Integer, ForeignKey("usuarios.id_usuario"))
    id_medico = Column(Integer, ForeignKey("medicos.id_medico"))
    fecha = Column(Date, nullable=False)
    hora = Column(Time, nullable=False)
    estado = Column(Enum('pendiente','aprobada','rechazada','cancelada'), default='pendiente')
    motivo_cancelacion = Column(Text)
    enlace_meet = Column(String(255))
    mensaje_medico = Column(Text)
    created_at = Column(TIMESTAMP, default=datetime.utcnow)
    deleted_at = Column(DateTime, nullable=True)  # soft delete timestamp

class PacienteMedico(Base):
    __tablename__ = "pacientes_medicos"
    id = Column(Integer, primary_key=True, index=True, autoincrement=True)
    id_medico = Column(Integer, ForeignKey("medicos.id_medico"))
    id_usuario = Column(Integer, ForeignKey("usuarios.id_usuario"))

class Diagnostico(Base):
    __tablename__ = "diagnosticos"
    id_diagnostico = Column(Integer, primary_key=True, index=True, autoincrement=True)
    id_cita = Column(Integer, ForeignKey("citas.id_cita"))
    descripcion = Column(Text)
    fecha = Column(TIMESTAMP, default=datetime.utcnow)

class ArticuloMedico(Base):
    __tablename__ = "articulos_medicos"
    id_articulo = Column(Integer, primary_key=True, index=True, autoincrement=True)
    id_medico = Column(Integer, ForeignKey("medicos.id_medico"))
    titulo = Column(String(255), nullable=False)
    categoria = Column(String(100), nullable=True)
    contenido = Column(Text, nullable=False)
    imagen_portada = Column(String(255), nullable=True)
    fecha_publicacion = Column(TIMESTAMP, default=datetime.utcnow)
    estatus = Column(Integer, default=1)

class Chat(Base):
    __tablename__ = "chats"
    id_chat = Column(Integer, primary_key=True, index=True, autoincrement=True)
    id_usuario = Column(Integer, ForeignKey("usuarios.id_usuario"))
    id_medico = Column(Integer, ForeignKey("medicos.id_medico"))
    estatus = Column(Enum('activo', 'restringido'), default='activo')

class Mensaje(Base):
    __tablename__ = "mensajes"
    id_mensaje = Column(Integer, primary_key=True, index=True, autoincrement=True)
    id_chat = Column(Integer, ForeignKey("chats.id_chat"))
    emisor = Column(Enum('usuario', 'medico'), nullable=False)
    mensaje = Column(Text, nullable=False)
    fecha_envio = Column(TIMESTAMP, default=datetime.utcnow)

class Notificacion(Base):
    __tablename__ = "notificaciones"
    id_notificacion = Column(Integer, primary_key=True, index=True, autoincrement=True)
    id_usuario = Column(Integer, ForeignKey("usuarios.id_usuario"))
    mensaje = Column(Text, nullable=False)
    leido = Column(Boolean, default=False)
    tipo = Column(String(50), default='info')
    fecha = Column(TIMESTAMP, default=datetime.utcnow)
