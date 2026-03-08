from flask import Flask, render_template

app = Flask(__name__)

# --- Auth Routes ---
@app.route("/")
def home():
    return "Flask funcionando correctamente"

@app.route('/login')
def login():
    return render_template('login.html')

@app.route('/registro')
def registro():
    return render_template('registro_usuario.html')

# --- Patient Routes ---
@app.route('/patient/dashboard')
def patient_dashboard():
    return render_template('patient/dashboard.html')

@app.route('/patient/specialists')
def patient_specialists():
    return render_template('patient/specialists.html')

@app.route('/patient/appointments')
def patient_appointments():
    return render_template('patient/appointments.html')

@app.route('/patient/articles')
def patient_articles():
    return render_template('patient/articles.html')

@app.route('/patient/tips')
def patient_tips():
    return render_template('patient/tips.html')

@app.route('/patient/profile')
def patient_profile():
    return render_template('patient/profile.html')

@app.route('/patient/about')
def patient_about():
    return render_template('patient/about.html')

@app.route('/patient/chat')
def patient_chat():
    return render_template('patient/chat.html')

# --- Doctor Routes ---
@app.route('/doctor/dashboard')
def doctor_dashboard():
    return render_template('doctor/dashboard.html')

@app.route('/doctor/appointments')
def doctor_appointments():
    return render_template('doctor/appointments.html')

@app.route('/doctor/patients')
def doctor_patients():
    return render_template('doctor/patients.html')

@app.route('/doctor/chat')
def doctor_chat():
    return render_template('doctor/chat.html')

@app.route('/doctor/content')
def doctor_content():
    return render_template('doctor/content.html')

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000)