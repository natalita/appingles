<?php
$unidadQuery = "SELECT id_unidad, descripcion FROM unidad";
$unidadResult = mysqli_query($con, $unidadQuery);

if (!$unidadResult) {
    die('Error al obtener las unidades: ' . mysqli_error($con));
}

$unidades = mysqli_fetch_all($unidadResult, MYSQLI_ASSOC);

$unidadRecursoQuery = "SELECT id_recurso, id_unidad, recurso_name, tipo_archivo, location, vtt_location, descripcion FROM recurso";
$unidadRecursoResult = mysqli_query($con, $unidadRecursoQuery);

if (!$unidadRecursoResult) {
    die('Error al obtener los recursos de las unidades: ' . mysqli_error($con));
}

$unidadesRecursos = mysqli_fetch_all($unidadRecursoResult, MYSQLI_ASSOC);

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper content-tests">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Main row -->
            <div class="row">
                <!-- Left col -->
                <div class="col-lg-12">
                    <div class="card card-primary bg-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Welcome to Quiz!</h3>
                        </div>
                        <!-- Botones de Unidades -->
                        <div class="unidad-buttons mx-auto">
                            <?php foreach ($unidades as $unidad) : ?>
                                <button class="unidad-btn btn bg-info font-weight-bold text-uppercase m-2" data-id="<?= $unidad['id_unidad']; ?>">
                                    <?= $unidad['descripcion']; ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- /.card -->

                    <!-- Card oculto con mensaje de 'All the tests related with this unity has been done' -->
                    <div class="card card-info bg-light text-center" id="noTestsCard" style="display: none;">
                        <div class="card-header">
                            <h3 class="card-title">There are no pending tests in this unit</h3>
                        </div>
                        <div class="card-body">
                            All tests for this unit have been completed.
                        </div>
                    </div>
                </div>
                <!-- start Quiz button -->
                <div class="start_btn" hidden><button id="startQuizBtn">Start Quiz</button></div>

                <!-- Info Box -->
                <div class="info_box">
                    <div class="info-title"><span>Some Rules of this Quiz</span></div>
                    <div class="info-list">
                        <div class="info">1. You will have only <span>90 seconds</span> per each question.</div>
                        <div class="info">2. Once you select your answer, it can't be undone.</div>
                        <div class="info">3. You can't select any option once time goes off.</div>
                        <div class="info">4. You can't exit from the Quiz while you're playing.</div>
                        <div class="info">5. You'll get points on the basis of your correct answers.</div>
                    </div>
                    <div class="buttons">
                        <button class="quit">Exit Quiz</button>
                        <button class="restart">Continue</button>
                    </div>
                </div>

                <!-- Quiz Box -->
                <div class="quiz_box">
                    <header>
                        <div class="title">Awesome Quiz Application</div>
                        <div class="timer">
                            <div class="time_left_txt">Time Left</div>
                            <div class="timer_sec">90</div>
                        </div>
                        <div class="time_line"></div>
                    </header>
                    <section>
                        <div class="que_text">
                            <!-- Here I've inserted question from JavaScript -->
                        </div>
                        <div class="option_list">
                            <!-- Here I've inserted options from JavaScript -->
                        </div>
                    </section>

                    <!-- footer of Quiz Box -->
                    <footer>
                        <div class="total_que">
                            <!-- Here I've inserted Question Count Number from JavaScript -->
                        </div>
                        <button class="next_btn">Next Que</button>
                    </footer>
                </div>

                <!-- Result Box -->
                <div class="result_box">
                    <div class="icon">
                        <i class="fas fa-crown"></i>
                    </div>
                    <div class="complete_text">You've completed the Quiz!</div>
                    <div class="score_text">
                        <!-- Here I've inserted Score Result from JavaScript -->
                    </div>
                    <div class="buttons">
                        <button class="restart">Refresh page</button>
                        <button class="quit">Quit Quiz</button>
                    </div>
                </div>

                <!-- Agregamos un input oculto para almacenar el ID del usuario -->
                <input type="hidden" id="userId" value="<?php echo $_SESSION['id_usuario']; ?>">

                




            </div>
            <!-- /.col -->

            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->