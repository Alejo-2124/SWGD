<?php require 'views/layout/header.php'; ?>

<div class="dashboard-header">
    <h1>Lista de Pacientes</h1>
    <p>Gestión completa de pacientes del sistema</p>
    <a href="<?php echo BASE_URL; ?>/dashboard" class="btn btn-secondary">← Volver al Dashboard</a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Pacientes Registrados en el Sistema</h3>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>Edad</th>
                    <th>Género</th>
                    <th>Teléfono</th>
                    <th>Fecha Registro</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Obtener todos los pacientes como array
                $patients_data = $patients->fetchAll(PDO::FETCH_ASSOC);
                
                if(count($patients_data) > 0): ?>
                    <?php foreach($patients_data as $patient): ?>
                        <tr>
                            <td><?php echo $patient['id']; ?></td>
                            <td><?php echo htmlspecialchars($patient['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($patient['email']); ?></td>
                            <td>
                                <?php 
                                if (isset($patient['edad']) && $patient['edad'] !== null && $patient['edad'] !== '') {
                                    echo htmlspecialchars($patient['edad']);
                                } else {
                                    echo '<span style="color: #999;">N/A</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php 
                                if (isset($patient['genero']) && !empty($patient['genero'])) {
                                    echo htmlspecialchars(ucfirst($patient['genero']));
                                } else {
                                    echo '<span style="color: #999;">N/A</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php 
                                if (isset($patient['telefono']) && !empty($patient['telefono'])) {
                                    echo htmlspecialchars($patient['telefono']);
                                } else {
                                    echo '<span style="color: #999;">N/A</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php 
                                if (isset($patient['created_at']) && !empty($patient['created_at'])) {
                                    echo date('d/m/Y', strtotime($patient['created_at']));
                                } else {
                                    echo '<span style="color: #999;">N/A</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem;">
                            No hay pacientes registrados en el sistema.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require 'views/layout/footer.php'; ?>