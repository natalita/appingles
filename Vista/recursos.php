<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Main content -->
	<section class="content content-resources">
		<div class="container-fluid">
			<!-- Main row -->
			<div class="row">
				<!-- Left col -->
				<div class="col-lg-12">
					<?php if ($_SESSION['rol'] != 'student'): ?>
						<div class="card card-primary">
							<div class="card-header">
								<h3 class="card-title">Welcome to Resources!</h3>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								<div class="info-box bg-success">
									<span class="info-box-icon"><i class="fas fa-play"></i></span>
									<div class="info-box-content">
										<h4 class="info-box-text">Watch all Resources</h4>
										<button type="button" class="btn btn-outline-light text-ellipsis" onclick="window.location.href='panel.php?modulo=ver_recurso'">Watch
												Resources</button>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="info-box bg-info">
									<span class="info-box-icon"><i class="fas fa-plus"></i></span>
									<div class="info-box-content">
										<h4 class="info-box-text">Upload files</h4>
										<button type="button" class="btn btn-outline-light text-ellipsis"
											data-toggle="modal" data-target="#form_modal"><b>Add file</b>
										</button>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="info-box bg-danger">
									<span class="info-box-icon"><i class="fas fa-trash"></i></span>
									<div class="info-box-content">
										<h4 class="info-box-text">Delete Resources</h4>
										<button type="button" class="btn btn-outline-light text-ellipsis" onclick="window.location.href='panel.php?modulo=eliminar_recurso'">Delete Resources</button>
									</div>
								</div>
							</div>
							<?php
							?>
							<?php
							include_once '../Config/conexion.php';

							// Consulta para obtener las unidades y los recursos asociados
							$query = "SELECT u.id_unidad, u.unidad, u.descripcion AS descripcion_unidad, r.id_recurso, r.recurso_name, r.tipo_archivo, r.descripcion AS descripcion_archivo
									FROM unidad u
									LEFT JOIN recurso r ON u.id_unidad = r.id_unidad
									ORDER BY u.id_unidad ASC, r.id_recurso ASC";

							$result = mysqli_query($con, $query);

							// Inicializar la variable que almacenará el ID de la unidad actual
							$currentUnitId = null;
							$unitHasResources = false; // Variable para verificar si la unidad tiene recursos

							// Verificar si la consulta fue exitosa
							if ($result && mysqli_num_rows($result) > 0) {

								echo '<div class="col-md-12">';
								
								// Inicio del bucle para recorrer los resultados
								while ($row = mysqli_fetch_assoc($result)) {

									$unidadId = $row['id_unidad'];
									$nombreUnidad = $row['unidad'];
									$descripcionUnidad = $row['descripcion_unidad'];
									$nombreArchivo = $row['recurso_name'];
									$tipoArchivo = $row['tipo_archivo'];
									$descripcionArchivo = $row['descripcion_archivo'];

									// Verificar si hay cambio en la unidad actual
									if ($unidadId !== $currentUnitId) {
										// Si es una nueva unidad, imprimir el separador y el nombre de la unidad
										if ($currentUnitId !== null) {
											// Si la unidad no tiene recursos, mostrar mensaje dentro de la info-box
											if (!$unitHasResources) {
												echo '<div class="col-md-6">';
												echo '<div class="info-box bg-warning">';  // Cambié el color de fondo a amarillo (bg-warning)
												echo '<span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>'; // Ícono de advertencia
												echo '<div class="info-box-content">';
												echo '<h4 class="info-box-text">There\'s no resources associated to this unit.</h4>';
												echo '</div>';
												echo '</div>';
												echo '</div>';
											}
											echo '</div>'; // Cerrar la fila de recursos de la unidad anterior
										}

										// Resetear la bandera de recursos
										$unitHasResources = false;

										echo '<div class="unity' . $nombreUnidad . '">';
										echo '<h3>Unit: ' . $descripcionUnidad . '</h3>';
										echo '<div class="row">';
										$currentUnitId = $unidadId;
									}

									// Verificar si el recurso tiene datos (es decir, no es NULL)
									if (!empty($nombreArchivo)) {
										if ($tipoArchivo == 'video') {
											$icono_archivo = 'fas fa-file-video';
										} elseif ($tipoArchivo == 'audio') {
											$icono_archivo = 'fas fa-file-audio';
										}

										// Imprimir el recurso actual
										echo '<div class="col-md-6">';
										echo '<div class="info-box bg-dark">';
										echo '<span class="info-box-icon"><i class="' . $icono_archivo . '"></i></span>';
										echo '<div class="info-box-content">';
										echo '<h4 class="info-box-text">' . $nombreArchivo . '</h4>';
										
										// Mostrar descripción del archivo si existe
										if (!empty($descripcionArchivo)) {
											echo '<p class="info-box-description text-ellipsis">' . $descripcionArchivo . '</p>';
										}

										// Cambiar el formulario por un enlace que redirija al módulo ver_recurso
										echo '<a href="panel.php?modulo=ver_recurso&id_recurso=' . $row['id_recurso'] . '" class="btn btn-outline-light text-ellipsis">';
										echo '<b>' . $nombreArchivo . '</b>';
										echo '</a>';
										echo '</div>';
										echo '</div>';
										echo '</div>';

										// Establecer la bandera de que la unidad tiene recursos
										$unitHasResources = true;
									}
								}

								// Cerrar la última fila y el div de la última unidad
								if (!$unitHasResources) {
									// Si la última unidad no tiene recursos, mostrar mensaje dentro de la info-box
									echo '<div class="col-md-6">';
									echo '<div class="info-box bg-warning">';  // Cambié el color de fondo a amarillo (bg-warning)
									echo '<span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>'; // Ícono de advertencia
									echo '<div class="info-box-content">';
									echo '<h4 class="info-box-text">There\'s no resources associated to this unit.</h4>';
									echo '</div>';
									echo '</div>';
									echo '</div>';
								}
								echo '</div>'; // Cerrar la última fila
								echo '</div>';
							} else {
								// Si no hay unidades ni recursos
								echo 'No hay unidades o recursos disponibles.';
							}
							echo '</div>';

							// Liberar el resultado y cerrar la conexión
							mysqli_free_result($result);
							
							?>
							<div class="modal fade" id="form_modal" aria-hidden="true">
								<div class="modal-dialog">
									<form id="archivo_form" action="../Modelo/save_archive.php" method="POST"
										enctype="multipart/form-data">
										<div class="modal-content">
											<div class="modal-body">
												<div class="col-md-12">
													<div class="form-group">
														<label>File</label>
														<input type="file" name="archivo" class="form-control-file" />
													</div>
													<?php
													include_once '../Config/conexion.php';
													// Realizar la conexión a la base de datos y verificar si hay errores
													// Suponiendo que ya tienes la conexión establecida

													// Consulta para obtener todas las unidades disponibles
													$query_unidades = "SELECT * FROM unidad";
													$result_unidades = mysqli_query($con, $query_unidades);

													// Verificar si la consulta fue exitosa
													if ($result_unidades) {
														// Iniciar el select
														echo '<div class="form-group">
																<label>Unit</label>
																<select name="unidad" class="form-control-file">';
														
														// Iterar sobre los resultados de la consulta
														while ($row_unidad = mysqli_fetch_assoc($result_unidades)) {
															// Obtener el id y el nombre de la unidad
															$id_unidad = $row_unidad['id_unidad'];
															$nombre_unidad = $row_unidad['unidad'];
															$descripcion_unidad = $row_unidad['descripcion'];
															
															// Crear la opción del select
															echo "<option value='$id_unidad'>$nombre_unidad - $descripcion_unidad</option>";
														}
														
														// Cerrar el select
														echo '</select>
															</div>';
													} else {
														// Manejar el caso en que la consulta falle
														echo '<div class="form-group">';
															echo '<label>Unit</label>';
															echo "<br>Error while consulting unities, please write the number of unity";
															echo '<input type="text" name="unidad" class="form-control-file" />';
														echo '</div>';
													}

													
													?>
													<div class="form-group">
														<label>Subtitle File (VTT)</label>
														<input type="file" name="subtitulo" class="form-control-file">
													</div>
													<div class="form-group">
														<label>Description</label>
														<input type="text" name="descripcion" class="form-control-file" />
													</div>
												</div>
											</div>
											<div style="clear:both;"></div>
											<div class="modal-footer">
												<button type="button" class="btn btn-danger" data-dismiss="modal"><span
														class="glyphicon glyphicon-remove"></span>Close</button>
												<button name="save" class="btn btn-primary"><span
														class="glyphicon glyphicon-save"></span>
													Save</button>
											</div>
										</div>
									</form>
								</div>
							</div>
							<!-- /.card -->
						</div>
						<!-- /.col -->
					<?php else: ?>
                        <!-- Show error message for students -->
                        <div class="m-2 alert alert-danger" role="alert">
                            Your user role does not allow access to this page.
                        </div>
                    <?php endif; 
						// Cerrar la conexión a la base de datos
						mysqli_close($con);
					?>
				</div>
				<!-- /.row -->
			</div>
			<!-- /.container-fluid -->
	</section>
	<!-- /.content -->
</div>
<!-- /.content-wrapper -->