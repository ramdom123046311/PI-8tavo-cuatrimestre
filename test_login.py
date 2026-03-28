import requests, json

tests = [
    ('paciente@demo.com', 'demo'),
    ('doctor@demo.com', 'demo'),
    ('admin@demo.com', 'demo'),
]
for correo, pwd in tests:
    r = requests.post('http://fastapi:8001/api/login', json={'correo': correo, 'contrasena': pwd}, timeout=5)
    d = r.json()
    if r.status_code == 200:
        print(f"OK [{correo}] rol={d['user']['rol']} nombre={d['user']['nombre']}")
    else:
        print(f"FAIL [{correo}]: {d}")
