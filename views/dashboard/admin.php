<?php require 'views/layout/header.php'; ?>

<div class="dashboard-header">
    <h1>Panel Médico - <?php echo $_SESSION['user_name']; ?></h1>
    <p>Gestión de sus pacientes y documentos</p>
</div>

<?php if(isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>

<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
<?php endif; ?>

<div class="dashboard-grid">
    <!-- Card Agregar Paciente -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Agregar Nuevo Paciente</h3>
        </div>
        <form action="<?php echo BASE_URL; ?>/patients/add" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre Completo *</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="cedula">Cédula de Identidad *</label>
                <input type="text" id="cedula" name="cedula" class="form-control" placeholder="Ingrese la cédula del paciente" required>
                <small style="color: var(--text-secondary);">La cédula debe ser única para cada paciente</small>
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="edad">Edad</label>
                <input type="number" id="edad" name="edad" class="form-control" min="0" max="120">
            </div>

            <div class="form-group">
                <label for="genero">Género</label>
                <select name="genero" id="genero" class="form-control">
                    <option value="">Seleccionar...</option>
                    <option value="masculino">Masculino</option>
                    <option value="femenino">Femenino</option>
                    <option value="otro">Otro</option>
                </select>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="tel" id="telefono" name="telefono" class="form-control">
            </div>

            <div class="form-group">
                <label for="direccion">Dirección</label>
                <textarea id="direccion" name="direccion" class="form-control" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Agregar Paciente</button>
        </form>
    </div>

    <!-- Card Subida de Archivos -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Subir Nuevo Documento</h3>
        </div>
        <form action="<?php echo BASE_URL; ?>/documents/upload" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="paciente_id">Seleccionar Paciente *</label>
                <select name="paciente_id" id="paciente_id" class="form-control" required>
                    <option value="">-- Seleccione un paciente --</option>
                    <?php 
                    // Reiniciar el resultset para iterar de nuevo
                    $patients_data = $patients->fetchAll(PDO::FETCH_ASSOC);
                    foreach($patients_data as $patient): 
                    ?>
                        <option value="<?php echo $patient['id']; ?>">
                            <?php echo htmlspecialchars($patient['nombre']); ?> 
                            (<?php echo htmlspecialchars($patient['email']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="documento">Archivo *</label>
                <input type="file" name="documento" id="documento" class="form-control" required>
                <small style="color: var(--text-secondary);">Formatos: PDF, JPG, PNG, DOC. Máx: 10MB</small>
            </div>

            <button type="submit" class="btn btn-primary">Subir Documento</button>
        </form>
    </div>

    <!-- Card Información del Sistema -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Mi Información</h3>
        </div>
        <p>Bienvenido al panel médico. Desde aquí puede gestionar los documentos médicos de sus pacientes.</p>
        <br>
        <p><strong>Médico:</strong> <?php echo $_SESSION['user_name']; ?></p>
        <p><strong>Email:</strong> <?php echo $_SESSION['user_email'] ?></p>
        <p><strong>Total pacientes:</strong> <?php echo count($patients_data); ?></p>
        
        <div style="margin-top: 1rem;">
            <a href="<?php echo BASE_URL; ?>/patients/list" class="btn btn-secondary">Ver Mis Pacientes</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Documentos Recientes de Mis Pacientes</h3>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre Archivo</th>
                    <th>Paciente</th>
                    <th>Subido por</th>
                    <th>Fecha</th>
                    <th>Tamaño</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if($documents->rowCount() > 0): ?>
                    <?php while($doc = $documents->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($doc['nombre_archivo']); ?></td>
                            <td><?php echo htmlspecialchars($doc['paciente_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($doc['admin_nombre']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($doc['fecha_subida'])); ?></td>
                            <td><?php echo formatFileSize($doc['tamano']); ?></td>
                            <td>
                                <a href="<?php echo BASE_URL; ?>/documents/download?id=<?php echo $doc['id']; ?>" class="btn btn-sm btn-secondary">Descargar</a>
                                <a href="<?php echo BASE_URL; ?>/documents/delete?id=<?php echo $doc['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar este documento?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No hay documentos registrados para sus pacientes.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require 'views/layout/footer.php'; ?>