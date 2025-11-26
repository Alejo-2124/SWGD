<?php require 'views/layout/header.php'; ?>

<div class="dashboard-header">
    <h1>Lista de Mis Pacientes</h1>
    <p>Gestión completa de pacientes registrados por usted</p>
    <a href="<?php echo BASE_URL; ?>/dashboard" class="btn btn-secondary">← Volver al Dashboard</a>
</div>

<!-- Card de Búsqueda y Filtros -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h3 class="card-title">Buscar Pacientes</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo BASE_URL; ?>/patients/list">
            <div style="display: grid; grid-template-columns: 1fr auto auto; gap: 1rem; align-items: end;">
                <div>
                    <label for="search">Término de búsqueda:</label>
                    <input type="text" id="search" name="search" class="form-control" 
                            placeholder="Ingrese nombre o cédula..." 
                            value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                </div>
                
                <div>
                    <label for="filter_type">Filtrar por:</label>
                    <select name="filter_type" id="filter_type" class="form-control">
                        <option value="nombre" <?php echo ($_GET['filter_type'] ?? 'nombre') === 'nombre' ? 'selected' : ''; ?>>Nombre</option>
                        <option value="cedula" <?php echo ($_GET['filter_type'] ?? '') === 'cedula' ? 'selected' : ''; ?>>Cédula</option>
                    </select>
                </div>
                
                <div>
                    <button type="submit" class="btn btn-primary">Buscar</button>
                    <a href="<?php echo BASE_URL; ?>/patients/list" class="btn btn-secondary">Limpiar</a>
                </div>
            </div>
        </form>
        
        <?php if(isset($_GET['search']) && !empty($_GET['search'])): ?>
            <div style="margin-top: 1rem; padding: 0.75rem; background: #e8f4fd; border-radius: 4px;">
                <strong>Resultados de búsqueda:</strong> 
                Mostrando pacientes que coinciden con "<?php echo htmlspecialchars($_GET['search']); ?>" 
                por <?php echo ($_GET['filter_type'] ?? 'nombre') === 'nombre' ? 'nombre' : 'cédula'; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="card-title">Pacientes Registrados</h3>
        <span class="badge" style="background: var(--primary-color); color: white; padding: 0.5rem 1rem; border-radius: 20px;">
            Total: <?php echo $patients->rowCount(); ?> pacientes
        </span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Cédula</th>
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
                            <td>
                                <strong><?php echo htmlspecialchars($patient['cedula'] ?? 'N/A'); ?></strong>
                            </td>
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
                                    echo date('d/m/Y H:i', strtotime($patient['created_at']));
                                } else {
                                    echo '<span style="color: #999;">N/A</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 3rem;">
                            <?php if(isset($_GET['search']) && !empty($_GET['search'])): ?>
                                <div style="color: #666;">
                                    <h4>No se encontraron pacientes</h4>
                                    <p>No hay pacientes que coincidan con "<?php echo htmlspecialchars($_GET['search']); ?>"</p>
                                    <a href="<?php echo BASE_URL; ?>/patients/list" class="btn btn-primary">Ver todos los pacientes</a>
                                </div>
                            <?php else: ?>
                                <div style="color: #666;">
                                    <h4>No hay pacientes registrados</h4>
                                    <p>Comience agregando su primer paciente desde el dashboard.</p>
                                    <a href="<?php echo BASE_URL; ?>/dashboard" class="btn btn-primary">Agregar Primer Paciente</a>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.badge {
    background: var(--primary-color);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
}

.table th {
    background-color: #f8f9fa;
    color: var(--primary-color);
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    vertical-align: middle;
}
</style>

<?php require 'views/layout/footer.php'; ?>