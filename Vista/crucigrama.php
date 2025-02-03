<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Main -->
            <div class="d-flex justify-content-center">
                <!-- Left col -->
                <div class="card p-3">
                  <div class="mx-auto m-3">
                    <h2>Crossword</h2>
                    <b>Find where to place the word that describes the image</b>
                    <div class="words">
                      <div class="horizontales">
                        <p><b>ACROSS</b></p>
                        <div class="wHor">
                          <img class="pictures" src="../Publico/img/crucigrama/palabra1.png"/>
                          <button id="btn" onclick="reproducirAudioCrucigrama(1)">Audio</button>
                          <div class="word" style="font-weight: bold">1</div>
                        </div>
                        <div class="wHor">
                          <img class="pictures" src="../Publico/img/crucigrama/palabra3.png"/>
                          <button id="btn" onclick="reproducirAudioCrucigrama(3)">Audio</button>
                          <div class="word" style="font-weight: bold">3</div>
                        </div>
                      </div>
                      <div class="verticales">
                          <p><b>DOWN</b></p>
                          <div class="wHor">
                            <img class="pictures" src="../Publico/img/crucigrama/palabra2.png"/>
                            <button id="btn" onclick="reproducirAudioCrucigrama(2)">Audio</button>
                            <div class="word" style="font-weight: bold">2</div>
                          </div>
                          <div class="wHor">
                            <img class="pictures" src="../Publico/img/crucigrama/palabra4.png"/>
                            <button id="btn" onclick="reproducirAudioCrucigrama(4)">Audio</button>
                            <div class="word" style="font-weight: bold">4</div>
                          </div>
                      </div>
                    </div>
                    <div id="mensaje"></div>
                  </div>
                  
                  <table class="table">
                    <tr>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila1C1" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila1C2" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila1C3" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila1C4" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila1C5" value="2" style="font-weight: bold"/>
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila1C6" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila1C7" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila1C8" />
                      </td>
                    </tr>
                    <tr>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila2C1" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila2C2" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila2C3" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila2C4" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila2C5" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila2C6" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila2C7" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila2C8" />
                      </td>
                    </tr>
                    <tr>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila3C1" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila3C2" value="1" style="font-weight: bold"/>
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila3C3" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila3C4" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila3C5" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila3C6" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila3C7" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila3C8" />
                      </td>
                    </tr>
                    <tr>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila4C1" value="3" style="font-weight: bold"/>
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila4C2" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila4C3" value="4" style="font-weight: bold"/>
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila4C4" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila4C5" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila4C6" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila4C7" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila4C8" />
                      </td>
                    </tr>
                    <tr>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila5C1" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila5C2" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila5C3" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila5C4" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila5C5" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila5C6" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila5C7" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila5C8" />
                      </td>
                    </tr>
                    <tr>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila6C1" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila6C2" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila6C3" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila6C4" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila6C5" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila6C6" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila6C7" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila6C8" />
                      </td>
                    </tr>
                    <tr>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila7C1" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila7C2" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila7C3" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila7C4" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila7C5" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila7C6" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila7C7" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila7C8" />
                      </td>
                    </tr>
                    <tr>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila8C1" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila8C2" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila8C3" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila8C4" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila8C5" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila8C6" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila8C7" />
                      </td>
                      <td style="padding: 0;">
                        <input class="casilla" type="text" maxlength="1" id="fila8C8" />
                      </td>
                    </tr>
                  </table>
                  <button onclick="verificar()">Verify</button>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->


<body>
  <div class="col-md-12">
    
    </div>
    
    
  </div>
</body>

</html>