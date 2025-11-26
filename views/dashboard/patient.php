<?php require 'views/layout/header.php'; ?>

<div class="dashboard-header">
    <h1>Mi Historial Médico</h1>
    <p>Bienvenido a su portal de paciente</p>
</div>

<!-- Información Personal del Paciente -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h3 class="card-title">Mi Información Personal</h3>
    </div>
    <div class="patient-info">
        <?php if($patient_info): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                <div>
                    <strong>Nombre:</strong> <?php echo htmlspecialchars($patient_info['nombre']); ?>
                </div>
                <div>
                    <strong>Email:</strong> <?php echo htmlspecialchars($patient_info['email']); ?>
                </div>
                <?php if(isset($patient_info['edad']) && $patient_info['edad']): ?>
                    <div>
                        <strong>Edad:</strong> <?php echo htmlspecialchars($patient_info['edad']); ?> años
                    </div>
                <?php endif; ?>
                <?php if(isset($patient_info['genero']) && $patient_info['genero']): ?>
                    <div>
                        <strong>Género:</strong> <?php echo htmlspecialchars(ucfirst($patient_info['genero'])); ?>
                    </div>
                <?php endif; ?>
                <?php if(isset($patient_info['telefono']) && $patient_info['telefono']): ?>
                    <div>
                        <strong>Teléfono:</strong> <?php echo htmlspecialchars($patient_info['telefono']); ?>
                    </div>
                <?php endif; ?>
                <?php if(isset($patient_info['direccion']) && $patient_info['direccion']): ?>
                    <div style="grid-column: 1 / -1;">
                        <strong>Dirección:</strong> <?php echo htmlspecialchars($patient_info['direccion']); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Documentos Médicos -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Mis Documentos Médicos</h3>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre del Documento</th>
                    <th>Médico Responsable</th>
                    <th>Fecha de Subida</th>
                    <th>Tamaño</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if($documents->rowCount() > 0): ?>
                    <?php while($doc = $documents->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($doc['nombre_archivo']); ?></td>
                            <td><?php echo htmlspecialchars($doc['admin_nombre']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($doc['fecha_subida'])); ?></td>
                            <td><?php echo formatFileSize($doc['tamano']); ?></td>
                            <td>
                                <a href="<?php echo BASE_URL; ?>/documents/download?id=<?php echo $doc['id']; ?>" 
                                    class="btn btn-sm btn-secondary">Descargar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem;">
                            No tienes documentos médicos asignados todavía.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require 'views/layout/footer.php'; ?>