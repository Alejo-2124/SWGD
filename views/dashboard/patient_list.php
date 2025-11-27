<?php require 'views/layout/header.php'; ?>

<div class="dashboard-header">
    <h1>Lista de Mis Pacientes</h1>
    <p>Gestión completa de pacientes registrados por usted</p>
    <div class="header-actions">
        <a href="<?php echo BASE_URL; ?>/dashboard" class="btn btn-secondary"> Volver al inicio</a>
    </div>
</div>

<!-- Card de Búsqueda y Filtros -->
<div class="card mb-2">
    <div class="card-header">
        <h3 class="card-title">Buscar Pacientes</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo BASE_URL; ?>/patients/list">
            <div class="search-grid">
                <div class="search-field">
                    <label for="search">Término de búsqueda:</label>
                    <input type="text" id="search" name="search" class="form-control" 
                            placeholder="Ingrese nombre o cédula..." 
                            value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                </div>
                
                <div class="filter-field">
                    <label for="filter_type">Filtrar por:</label>
                    <select name="filter_type" id="filter_type" class="form-control">
                        <option value="nombre" <?php echo ($_GET['filter_type'] ?? 'nombre') === 'nombre' ? 'selected' : ''; ?>>Nombre</option>
                        <option value="cedula" <?php echo ($_GET['filter_type'] ?? '') === 'cedula' ? 'selected' : ''; ?>>Cédula</option>
                    </select>
                </div>
                
                <div class="search-actions">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                    <a href="<?php echo BASE_URL; ?>/patients/list" class="btn btn-outline">Limpiar</a>
                </div>
            </div>
        </form>
        
        <?php if(isset($_GET['search']) && !empty($_GET['search'])): ?>
            <div class="search-results">
                <strong>Resultados de búsqueda:</strong> 
                Mostrando pacientes que coinciden con "<?php echo htmlspecialchars($_GET['search']); ?>" 
                por <?php echo ($_GET['filter_type'] ?? 'nombre') === 'nombre' ? 'nombre' : 'cédula'; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Mostrar mensajes de éxito/error -->
<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
<?php endif; ?>

<?php if(isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="card-title">Pacientes Registrados</h3>
        <span class="badge">
            Total: <?php echo $patients->rowCount(); ?> pacientes
        </span>
    </div>
    <div class="table-responsive">
        <table class="table patients-table">
            <thead>
                <tr>
                    <th>Nombre Completo</th>
                    <th>Edad</th>
                    <th>Género</th>
                    <th>Cédula</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Fecha Registro</th>
                    <th>Documentos</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Obtener todos los pacientes como array
                $patients_data = $patients->fetchAll(PDO::FETCH_ASSOC);
                
                if(count($patients_data) > 0): ?>
                    <?php foreach($patients_data as $index => $patient): 
                        // Obtener documentos del paciente actual
                        $documentModel = new Document();
                        $patientDocuments = $documentModel->getDocumentsByPatient($patient['id']);
                        $documents_data = $patientDocuments->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                        <tr class="patient-row <?php echo $index % 2 === 0 ? 'even' : 'odd'; ?>">
                            <td class="patient-name">
                                <strong><?php echo htmlspecialchars($patient['nombre']); ?></strong>
                            </td>
                            <td>
                                <?php 
                                if (isset($patient['edad']) && $patient['edad'] !== null && $patient['edad'] !== '') {
                                    echo htmlspecialchars($patient['edad']) . ' años';
                                } else {
                                    echo '<span class="na">N/A</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php 
                                if (isset($patient['genero']) && !empty($patient['genero'])) {
                                    echo htmlspecialchars(ucfirst($patient['genero']));
                                } else {
                                    echo '<span class="na">N/A</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($patient['cedula'] ?? 'N/A'); ?>
                            </td>
                            <td><?php echo htmlspecialchars($patient['email']); ?></td>
                            <td>
                                <?php 
                                if (isset($patient['telefono']) && !empty($patient['telefono'])) {
                                    echo htmlspecialchars($patient['telefono']);
                                } else {
                                    echo '<span class="na">N/A</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php 
                                if (isset($patient['created_at']) && !empty($patient['created_at'])) {
                                    echo date('d/m/Y', strtotime($patient['created_at']));
                                } else {
                                    echo '<span class="na">N/A</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <span class="document-count">
                                    <?php echo count($documents_data); ?> doc.
                                </span>
                            </td>
                        </tr>
                        
                        <!-- Fila expandible para documentos del paciente -->
                        <tr class="documents-row <?php echo $index % 2 === 0 ? 'even' : 'odd'; ?>">
                            <td colspan="8" class="documents-cell">
                                <div class="documents-container">
                                    <h4>📁 Documentos de <?php echo htmlspecialchars($patient['nombre']); ?></h4>
                                    
                                    <?php if(count($documents_data) > 0): ?>
                                        <div class="documents-table-container">
                                            <table class="documents-table">
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
                                                    <?php foreach($documents_data as $doc): ?>
                                                        <tr>
                                                            <td class="filename-cell">
                                                                <span class="document-icon">
                                                                    <?php 
                                                                    $extension = pathinfo($doc['nombre_archivo'], PATHINFO_EXTENSION);
                                                                    switch(strtolower($extension)) {
                                                                        case 'pdf': echo '📄'; break;
                                                                        case 'jpg': case 'jpeg': case 'png': echo '🖼️'; break;
                                                                        case 'doc': case 'docx': echo '📝'; break;
                                                                        default: echo '📎';
                                                                    }
                                                                    ?>
                                                                </span>
                                                                <span class="filename-text" title="<?php echo htmlspecialchars($doc['nombre_archivo']); ?>">
                                                                    <?php echo htmlspecialchars($doc['nombre_archivo']); ?>
                                                                </span>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($doc['admin_nombre']); ?></td>
                                                            <td><?php echo date('d/m/Y H:i', strtotime($doc['fecha_subida'])); ?></td>
                                                            <td><?php echo formatFileSize($doc['tamano']); ?></td>
                                                            <td>
                                                                <div class="document-actions">
                                                                    <a href="<?php echo BASE_URL; ?>/documents/view?id=<?php echo $doc['id']; ?>" 
                                                                        class="btn btn-sm btn-eye" 
                                                                        target="_blank"
                                                                        title="Ver documento en el navegador">
                                                                        <span class="btn-icon">👁️</span> Ver
                                                                    </a>
                                                                    <a href="<?php echo BASE_URL; ?>/documents/download?id=<?php echo $doc['id']; ?>" 
                                                                        class="btn btn-sm btn-secondary" 
                                                                        title="Descargar documento">
                                                                        <span class="btn-icon">⬇️</span> Descargar
                                                                    </a>
                                                                    <a href="<?php echo BASE_URL; ?>/documents/delete?id=<?php echo $doc['id']; ?>&redirect_to=patient_list" 
                                                                        class="btn btn-sm btn-danger" 
                                                                        onclick="return confirm('¿Está seguro de eliminar el documento \\'<?php echo addslashes($doc['nombre_archivo']); ?>\'?');"
                                                                        title="Eliminar documento">
                                                                        <span class="btn-icon">🗑️</span> Eliminar
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="no-documents">
                                            <p>📭 No hay documentos médicos asignados a este paciente.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="no-patients">
                            <?php if(isset($_GET['search']) && !empty($_GET['search'])): ?>
                                <div class="no-results">
                                    <h4>No se encontraron pacientes</h4>
                                    <p>No hay pacientes que coincidan con "<?php echo htmlspecialchars($_GET['search']); ?>"</p>
                                    <a href="<?php echo BASE_URL; ?>/patients/list" class="btn btn-primary">Ver todos los pacientes</a>
                                </div>
                            <?php else: ?>
                                <div class="no-results">
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

<?php require 'views/layout/footer.php'; ?>