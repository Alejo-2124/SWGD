<?php require 'views/layout/header.php'; ?>

<div class="dashboard-header">
    <h1>Mi Historial Médico</h1>
    <p>Consulta y descarga tus documentos médicos</p>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Mis Documentos</h3>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre Archivo</th>
                    <th>Médico Responsable</th>
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
                            <td><?php echo htmlspecialchars($doc['admin_nombre']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($doc['fecha_subida'])); ?></td>
                            <td><?php echo formatFileSize($doc['tamano']); ?></td>
                            <td>
                                <a href="<?php echo BASE_URL; ?>/documents/download?id=<?php echo $doc['id']; ?>" class="btn btn-sm btn-secondary">Descargar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No tienes documentos asignados todavía.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require 'views/layout/footer.php'; ?>
