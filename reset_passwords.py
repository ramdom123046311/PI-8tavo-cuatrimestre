import sys
sys.path.insert(0, '/app')
from app.database import SessionLocal
from app.models.models import Usuario
from app.services.auth import get_password_hash, verify_password

db = SessionLocal()

# Define demo credentials
demo_users = {
    'paciente@demo.com': 'demo',
    'doctor@demo.com': 'demo',
    'admin@demo.com': 'demo',
}

for correo, plain_pwd in demo_users.items():
    u = db.query(Usuario).filter(Usuario.correo == correo).first()
    if u:
        # Force reset to fresh hash
        u.contrasena = get_password_hash(plain_pwd)
        print(f"Reset password for {correo}")
    else:
        print(f"User not found: {correo}")

db.commit()
print("Done. Verifying...")

for correo, plain_pwd in demo_users.items():
    u = db.query(Usuario).filter(Usuario.correo == correo).first()
    if u:
        ok = verify_password(plain_pwd, u.contrasena)
        print(f"  {correo} => verify_password('{plain_pwd}') = {ok}")

db.close()
