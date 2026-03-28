from pydantic import BaseModel, EmailStr
from datetime import date, time, datetime
from typing import Optional, List

class TokenUser(BaseModel):
    id: int
    rol: int
    nombre: str
    apellido: Optional[str] = None
    tiene_foto: bool = False

class Token(BaseModel):
    access_token: str
    token_type: str
    user: Optional[TokenUser] = None

class TokenData(BaseModel):
    correo: Optional[str] = None

class UsuarioBase(BaseModel):
    nombre: str
    apellido_paterno: str
    genero: str
    fecha_nacimiento: date
    telefono: Optional[str] = None
    correo: EmailStr
    id_rol: int

class UsuarioCreate(UsuarioBase):
    contrasena: str
    cp: Optional[str] = None
    especialidad: Optional[str] = None
    anios_experiencia: Optional[int] = None
    
class LoginSchema(BaseModel):
    correo: EmailStr
    contrasena: str

class UsuarioResponse(UsuarioBase):
    id_usuario: int
    estatus: int
    fecha_registro: datetime
    foto_perfil: Optional[str] = None
    class Config:
        orm_mode = True

class MedicoBase(BaseModel):
    cp: Optional[str] = None
    especialidad: Optional[str] = None
    descripcion_profesional: Optional[str] = None
    anios_experiencia: Optional[int] = None

class MedicoCreate(MedicoBase):
    id_usuario: int

class MedicoResponse(MedicoBase):
    id_medico: int
    id_usuario: int
    estatus: str
    aprobado: bool
    class Config:
        orm_mode = True

class CitaBase(BaseModel):
    id_usuario: int
    id_medico: int
    fecha: date
    hora: time
    motivo_cancelacion: Optional[str] = None
    mensaje_medico: Optional[str] = None

class CitaCreate(CitaBase):
    pass

class CitaResponse(CitaBase):
    id_cita: int
    estado: str
    enlace_meet: Optional[str] = None
    created_at: datetime
    class Config:
        orm_mode = True

class ArticuloBase(BaseModel):
    id_medico: int
    titulo: str
    categoria: Optional[str] = None
    contenido: str
    imagen_portada: Optional[str] = None
    estatus: Optional[int] = 1

class ArticuloCreate(ArticuloBase):
    pass

class ArticuloResponse(ArticuloBase):
    class Config:
        orm_mode = True

class NotificacionBase(BaseModel):
    id_usuario: int
    mensaje: str
    tipo: Optional[str] = 'info'

class NotificacionCreate(NotificacionBase):
    pass

class NotificacionResponse(NotificacionBase):
    id_notificacion: int
    leido: bool
    fecha: datetime
    class Config:
        orm_mode = True

class ChatBase(BaseModel):
    id_usuario: int
    id_medico: int

class ChatCreate(ChatBase):
    pass

class ChatResponse(ChatBase):
    id_chat: int
    estatus: str
    class Config:
        orm_mode = True

class MensajeBase(BaseModel):
    id_chat: int
    emisor: str
    mensaje: str

class MensajeCreate(MensajeBase):
    pass

class MensajeResponse(MensajeBase):
    id_mensaje: int
    fecha_envio: datetime
    class Config:
        orm_mode = True

class DiagnosticoBase(BaseModel):
    id_cita: int
    descripcion: str

class DiagnosticoCreate(DiagnosticoBase):
    pass

class DiagnosticoResponse(DiagnosticoBase):
    id_diagnostico: int
    fecha: datetime
    class Config:
        orm_mode = True
