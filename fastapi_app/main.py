from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
import os

app = FastAPI(title="Salud Materna API")

# Setup CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

@app.get("/api/health")
def health_check():
    return {"status": "ok", "service": "FastAPI"}

@app.get("/api/patient/dashboard-data")
def get_patient_data():
    return {"message": "Patient data fetched from FastAPI"}

@app.get("/api/doctor/dashboard-data")
def get_doctor_data():
    return {"message": "Doctor data fetched from FastAPI"}

# We will run this via uvicorn later
