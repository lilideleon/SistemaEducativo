<?php
// Perfil - cambiar contraseña (diseño mejorado)
require_once "core/AuthMiddleware.php";
$user = AuthMiddleware::getCurrentUser();
?>

<!-- Asegurarnos de que Bootstrap está cargado -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<style>
/* Estilos mejorados para el perfil */
:root {
    --bs-primary: #4f8f8a;
    --bs-primary-rgb: 79,143,138;
}

body {
    background-color: #f8f9fa;
}

.btn-primary {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
}

.btn-primary:hover {
    background-color: #2e655f;
    border-color: #2e655f;
}

/* Mejoras de centrado y espaciado */
.card {
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,.125);
    background-color: #ffffff;
}

.card-body {
    padding: 2rem;
}

.form-control {
    padding: .75rem 1rem;
}

.input-group .btn {
    padding-left: 1rem;
    padding-right: 1rem;
}

/* Responsive fixes */
@media (max-width: 768px) {
    .card-body {
        padding: 1.5rem;
    }
    
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}

/* Progress bar styling */
.progress {
    height: 8px !important;
    margin-top: 0.5rem;
    border-radius: 4px;
    background-color: #e9ecef;
}

.progress-bar {
    background-color: var(--bs-primary);
    transition: width 0.3s ease;
}

.perfil-container { 
    max-width: 820px; 
    margin: 20px auto;
    padding: 20px 15px;
    position: relative;
    z-index: 1;
}

.perfil-card { 
    background: #ffffff;
    border: 1px solid var(--theme-border);
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 2rem;
    overflow: hidden;
    position: relative;
    z-index: 2;
    padding: 1.5rem;
}

.small-muted { 
    color: var(--theme-text);
    font-size: 0.9rem;
    opacity: 0.8;
    margin-bottom: 0.5rem;
    display: block;
}

/* Form styling */
.form-control {
    border: 2px solid #e0e6ed;
    border-radius: 8px;
    padding: 0.6rem 1rem;
    transition: all 0.2s ease;
    background: #ffffff;
    color: var(--theme-text);
    font-size: 1rem;
    line-height: 1.5;
    height: auto;
    margin-bottom: 0.5rem;
}

.form-control:focus {
    border-color: var(--theme-accent);
    box-shadow: 0 0 0 0.25rem rgba(111, 183, 178, 0.15);
}

.input-group .btn {
    border-top-right-radius: 8px !important;
    border-bottom-right-radius: 8px !important;
    padding: 0.6rem 1rem;
}

/* Password strength meter */
.pw-strength { 
    height: 8px; 
    border-radius: 10px; 
    background: #e9ecef;
    overflow: hidden;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
}

.pw-strength > i { 
    display: block; 
    height: 100%; 
    width: 0%; 
    background: linear-gradient(90deg,
        var(--theme-dark) 0%,
        var(--theme-primary) 50%,
        var(--theme-accent) 100%
    );
    transition: width 0.3s ease;
    border-radius: 10px;
}

.pw-meta { 
    font-size: 0.85rem;
    color: var(--theme-secondary);
}

/* Button styling */
.btn-glow {
    position: relative;
    background: #4f8f8a;
    color: white;
    border: none;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    font-weight: 500;
    letter-spacing: 0.3px;
    transition: all 0.3s ease;
    z-index: 1;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-glow:hover {
    background: var(--theme-secondary);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(79, 143, 138, 0.2);
}

.btn-glow:active {
    transform: translateY(0);
}

.btn-outline-secondary {
    color: var(--theme-secondary);
    border-color: var(--theme-secondary);
    background: transparent;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    background: var(--theme-secondary);
    color: white;
}

.show-pass-btn { 
    cursor: pointer;
    color: var(--theme-secondary);
    border-color: #e0e6ed;
}

.show-pass-btn:hover {
    background: var(--theme-light);
    border-color: var(--theme-accent);
}

/* Card styling */
.card {
    border: none;
    box-shadow: 0 2px 15px rgba(0,0,0,0.05);
}

.card .card-body {
    padding: 1.5rem;
    background: #ffffff;
    position: relative;
    z-index: 3;
}

.card-body h6 {
    font-size: 1.1rem;
    margin-bottom: 1.5rem;
    color: var(--theme-dark);
    border-bottom: 2px solid var(--theme-light);
    padding-bottom: 0.75rem;
}

/* Labels and headings */
.form-label {
    color: var(--theme-dark);
    font-weight: 500;
    margin-bottom: 0.5rem;
}

h4, h5, h6 {
    color: var(--theme-dark);
    font-weight: 600;
}

/* Alert styling */
.alert {
    border: none;
    border-radius: 8px;
    padding: 1rem;
    margin-top: 1rem;
}

.alert-success {
    background: var(--theme-light);
    color: var(--theme-secondary);
    border-left: 4px solid var(--theme-accent);
}

.alert-danger {
    background: #fff5f5;
    color: #dc3545;
    border-left: 4px solid #dc3545;
}

/* Responsive adjustments */
@media (max-width: 767.98px) {
    .row {
        margin: 0 -5px;
    }
    
    .col-md-6 {
        padding: 0 5px;
    }
}
</style>

<div class="container py-4 px-3 px-md-4" style="max-width: 960px;">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mx-auto" style="max-width: 900px;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h5 class="mb-1"><?php echo htmlspecialchars(isset($user['nombres']) ? $user['nombres'] : '') . ' ' . htmlspecialchars(isset($user['apellidos']) ? $user['apellidos'] : ''); ?></h5>
                    <div class="small-muted">Usuario: <strong><?php echo htmlspecialchars(isset($user['nombre_completo']) ? $user['nombre_completo'] : ''); ?></strong></div>
                    <div class="small-muted">Rol: <?php echo htmlspecialchars(isset($user['rol']) ? $user['rol'] : ''); ?></div>
                </div>
            </div>

            <div class="card bg-light border-0 mt-4 mx-auto" style="max-width: 800px;">
                        <div class="card-body">
                    <h6 class="mb-3"><i class="bi bi-lock-fill me-2"></i>Cambiar contraseña</h6>
                    <form id="formPerfil" novalidate>
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Contraseña actual <span class="text-danger">*</span></label>
                            <div class="input-group mb-3">
                                <input type="password" class="form-control shadow-none" id="current_password" name="current_password" required placeholder="Ingrese su contraseña actual">
                                <button class="btn btn-outline-secondary d-flex align-items-center show-pass-btn" type="button" data-target="#current_password"><i class="bi bi-eye"></i></button>
                            </div>
                            <div class="form-text">Es necesario verificar su contraseña actual por seguridad.</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="new_password" class="form-label">Nueva contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    <button class="btn btn-outline-secondary show-pass-btn" type="button" data-target="#new_password"> <i class="bi bi-eye"></i> </button>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirmar nueva contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    <button class="btn btn-outline-secondary show-pass-btn" type="button" data-target="#confirm_password"> <i class="bi bi-eye"></i> </button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1 pw-meta">
                                <div id="pwLabel">Fortaleza de la contraseña</div>
                                <div id="pwScore" class="small-muted">0/4</div>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" id="pwBar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="mt-3 d-flex gap-2">
                            <button type="submit" class="btn btn-glow"><i class="bi bi-check-circle me-1"></i>Actualizar contraseña</button>
                            <button type="button" id="cancelBtn" class="btn btn-outline-secondary">Cancelar</button>
                        </div>
                        <div id="perfilMsg" class="mt-3" aria-live="polite"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Utility: show/hide password toggles
document.querySelectorAll('.show-pass-btn').forEach(function(btn){
    btn.addEventListener('click', function(){
        var target = document.querySelector(btn.getAttribute('data-target'));
        if (!target) return;
        if (target.type === 'password') { target.type = 'text'; btn.innerHTML = '<i class="bi bi-eye-slash"></i>'; }
        else { target.type = 'password'; btn.innerHTML = '<i class="bi bi-eye"></i>'; }
    });
});

// Password strength simple estimator
function scorePassword(pw) {
    var score = 0;
    if (!pw) return 0;
    if (pw.length >= 6) score++;
    if (pw.length >= 10) score++;
    if (/[0-9]/.test(pw)) score++;
    if (/[A-Z]/.test(pw) && /[a-z]/.test(pw)) score++;
    return Math.min(score,4);
}

var newPw = document.getElementById('new_password');
var pwBar = document.getElementById('pwBar');
var pwScore = document.getElementById('pwScore');
var pwLabel = document.getElementById('pwLabel');

newPw.addEventListener('input', function(){
    var s = scorePassword(newPw.value);
    var pct = (s/4)*100;
    pwBar.style.width = pct + '%';
    pwScore.textContent = s + '/4';
    var labels = ['Muy débil','Débil','Aceptable','Buena','Excelente'];
    pwLabel.textContent = 'Fortaleza: ' + labels[s];
    // color progress
    var color;
    switch(s){ case 0: case 1: color = '#dc3545'; break; case 2: color = '#ffc107'; break; case 3: color = '#17a2b8'; break; default: color = '#28a745'; }
    pwBar.style.background = color;
});

// Form submit
document.getElementById('formPerfil').addEventListener('submit', function(e){
    e.preventDefault();
    var current = document.getElementById('current_password').value;
    var nw = document.getElementById('new_password').value;
    var cf = document.getElementById('confirm_password').value;
    var msg = document.getElementById('perfilMsg');
    msg.innerHTML = '';

    if (!current) { msg.innerHTML = '<div class="alert alert-danger">Debe ingresar su contraseña actual.</div>'; return; }
    if (nw.length < 6) { msg.innerHTML = '<div class="alert alert-danger">La contraseña debe tener al menos 6 caracteres.</div>'; return; }
    if (nw !== cf) { msg.innerHTML = '<div class="alert alert-danger">La confirmación no coincide.</div>'; return; }

    var formData = new FormData();
    formData.append('current_password', current);
    formData.append('new_password', nw);
    formData.append('confirm_password', cf);

    fetch('?c=Perfil&a=ActualizarPassword', { method: 'POST', body: formData })
    .then(function(r){ return r.json(); })
    .then(function(data){
        if (data.success) {
            msg.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
            document.getElementById('formPerfil').reset();
            pwBar.style.width = '0%'; pwScore.textContent = '0/4'; pwLabel.textContent = 'Fortaleza de la contraseña';
            // Esperar 2 segundos antes de redirigir para que el usuario vea el mensaje de éxito
            setTimeout(function() {
                window.location.href = '?c=Login';  // Redirigir al login
            }, 2000);
        } else {
            msg.innerHTML = '<div class="alert alert-danger">' + (data.message || 'Error') + '</div>';
        }
    })
    .catch(function(err){
        msg.innerHTML = '<div class="alert alert-danger">Error de red</div>';
    });
});

document.getElementById('cancelBtn').addEventListener('click', function(){
    // Redirigir a la sección de evaluación
    window.location.href = '?c=Evaluacion';
});
</script>
