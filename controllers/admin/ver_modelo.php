<?php session_start();
  ob_start();
  require '../../routes/routes.php';
  require '../../models/functions.php';
  require '../../schema/database.php';

  $conexion = conexion($db_config);
  sesion();

  $usuario = $_SESSION['user'];
  $id_model = articulo($_GET['id']);
  $modelo = review_model($conexion, $id_model);
  $model = $modelo[0];
  $servicios = obtener_servicios_galeria($conexion);
  $fotos_model = review_fotos($conexion, $id_model);
  $videos_model = review_video($conexion, $id_model);
  $adm = obtener_admin($conexion, $usuario);

  if (!$adm) {
    header('Location: ' . route_admin);
  }

  $id = $id_model;
  $provincias = array('Provincia de Bocas del Toro',
    'Provincia de Coclé',
    'Provincia de Colón',
    'Provincia de Chiriquí',
    'Provincia de Darién',
    'Provincia de Herrera',
    'Provincia de Los Santos',
    'Provincia de Panamá',
    'Provincia de Veraguas',
    'Provincia de Panamá Oeste'
    );
  $distritos = obtener_distrito($conexion);
  $servicios = obtener_servicios($conexion);
  $servs_model = obtener_servicios_model($conexion, $id);
  $nacionalidades = obtener_nacionalidad($conexion);
  $edades = array(18,19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45);
  $alturas = array(145,146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 157, 157, 158, 159, 160, 161, 162, 163, 164, 165, 166, 167, 168, 169, 170, 171, 172, 173, 174, 175, 176, 177, 178, 179, 180, 181, 182, 183, 184, 185, 186);
  $colores_cabello = array('negro', 'rubio', 'rojo', 'castaño', 'verde', 'azul', 'morado');
  $colores_ojos = array('marrones', 'verdes', 'azules', 'morados', 'amarillos', 'negros', 'grises');
  $colores_piel = array('blanca', 'trigueña', 'morena', 'negra');
  $tamaño_pechos = array('32 a', '32 b', '32 c', '34 a', '34 b', '34 c','36 a', '36 b', '36 c','36 d', '38 a', '38 b','38 c', '38 d', '40 a', '40 b', '40 c', '40 d');
  $tarifas = array(100, 150, 200, 250, 300, 350, 400, 450, 500, 'acordar con cliente');
  $forma_pagos = array('sólo efectivo', 'efectivo y tarjeta', 'efectivo y transferencia bancaria');
  $orientaciones = array('heterosexual', 'homosexual', 'bisexual', 'otro');
  $dias_atencion = array('lunes a viernes', 'fines de semana', 'todos los días', 'varía');
  $horarios = array('6:00pm a 12:00am', '12:00m a 6:00pm', '3:00pm a 9:00pm', '24 horas' , 'varía');
  $lugares_atencion = array('sólo hoteles y moteles', 'hotel y apartamento', 'tengo habitación propia', 'otro');
  $responde = array('si', 'no');
  $limite_foto = obtener_fotos_id($conexion, $id);
  $limite = $limite_foto[0];
  $cantidad = $limite['cantidad_foto'];
  $limite_video = obtener_videos_id($conexion, $id);
  $limite_v = $limite_video[0];
  $cantidad_video = $limite_v['cantidad_video'];


  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($model['codigo'])) {
      $id = $model['codigo'];
    }
    if(isset($_POST["anuncio"])){
      if (isset($_POST["servicio"])) {
        $service = $_POST['servicio'];
      }
      if (isset($_POST['otro_serv'])) {
        $otros_serv = $_POST['otro_serv'];
      }


      $user_id = $model['codigo'];

      $user_new = array(
        filter_var($_POST['bio']),
        filter_var($_POST['color_pelo']),
        filter_var($_POST['color_ojos']),
        filter_var($_POST['color_piel']),
        filter_var($_POST['altura']),
        filter_var($_POST['pechos']),
        filter_var($_POST['dias_atencion']),
        filter_var($_POST['horario']),
        filter_var($_POST['lugar_atencion']),
        filter_var($_POST['whatsapp']),
        filter_var($_POST['nacionalidad']),
        filter_var($_POST['name']),
        filter_var($_POST['sexualidad']),
        filter_var($_POST['tarifa']),
        filter_var($_POST['cobro']),
        filter_var($_POST['phone']),
        filter_var($_POST['zone']),
        filter_var($_POST['province']),
        filter_var($_POST['edad']),
        filter_var($_POST['presentation']),
        filter_var($_POST['title']),
      );

      $user_guardado = array(
        $model['biografia'],
        $model['color_cabello'],
        $model['color_ojos'],
        $model['color_piel'],
        $model['altura'],
        $model['pechos'],
        $model['dias_atencion'],
        $model['horario'],
        $model['lugar_atencion'],
        $model['whatsapp'],
        $model['nacionalidad'],
        $model['nombre_model'],
        $model['orientacion'],
        $model['pago'],
        $model['forma_pago'],
        $model['telefono'],
        $model['zona'],
        $model['provincia'],
        $model['edad'],
        $model['presentacion'],
        $model['slogan']
      );

      $cont = 0;
      foreach ($user_new as $key) {

        if(empty($user_new[$cont]) || $user_new[$cont] == '' || $user_new[$cont] == $user_guardado[$cont]){
          $user_new[$cont] = $user_guardado[$cont];
        }

        $cont++;
      }

      $error = '';

      $cont = 0;
      foreach ($user_new as $key) {

        if(empty($user_new[$cont]) || $user_new[$cont] == '' || $user_new[$cont] == null){
          echo '<script language="javascript">alert("Debe Llenar todos los campos");</script>';
          $error .='No rellenó todos los campos';
        }

        $cont++;
      }

      $cont = 0;

      if (!isset($service)) {
        echo '<script language="javascript">alert("Debe Llenar todos los campos");</script>';
        $error .='No rellenó todos los campos';
      }

      if (!isset($otros_serv)) {
        echo '<script language="javascript">alert("Debe Llenar todos los campos");</script>';
        $error .='No rellenó todos los campos';
      }

      if ($error == '') {
        $statement = $conexion->prepare('UPDATE modelo SET
          biografia = :biografia,
          color_cabello = :color_cabello,
          color_ojos = :color_ojos,
          color_piel = :color_piel,
          altura = :altura,
          pechos = :pechos,
          dias_atencion = :dias_atencion,
          horario = :horario,
          lugar_atencion = :lugar_atencion,
          whatsapp = :whatsapp,
          nacionalidad = :nacionalidad,
          nombre_model = :nombre_model,
          orientacion = :orientacion,
          pago = :pago,
          forma_pago = :forma_pago,
          telefono = :telefono,
          zona = :zona,
          provincia = :provincia,
          edad = :edad,
          presentacion = :presentacion,
          slogan = :slogan
          WHERE codigo = :id');

        $statement->execute(array(
          ':biografia' =>$user_new[0],
          ':color_cabello' => $user_new[1],
          ':color_ojos' => $user_new[2],
          ':color_piel' => $user_new[3],
          ':altura' => $user_new[4],
          ':pechos' => $user_new[5],
          ':dias_atencion' => $user_new[6],
          ':horario' => $user_new[7],
          ':lugar_atencion' => $user_new[8],
          ':whatsapp' => $user_new[9],
          ':nacionalidad' => $user_new[10],
          ':nombre_model' => $user_new[11],
          ':orientacion' => $user_new[12],
          ':pago' => $user_new[13],
          ':forma_pago' => $user_new[14],
          ':telefono' => $user_new[15],
          ':zona' => $user_new[16],
          ':provincia' => $user_new[17],
          ':edad' => $user_new[18],
          ':presentacion' => $user_new[19],
          ':slogan' => $user_new[20],
          ':id' => $user_id
          ));
        $servicios = obtener_servicios($conexion);
        $otros_servicios = obtener_otros_servicios($conexion);
        $servs_model = obtener_servicios_model($conexion, $id);
        $otros_serv_model = obtener_otros_servicios_model($conexion, $id);
        $cont = 0;

        foreach ($servicios as $servi) {
          $check = 0;
          foreach ($service as $serv) {
            $control = 0;

            if ($servi['id_serv'] == $serv[$cont]) {
              $check = $serv[$cont];
              foreach ($servs_model as $key) {
                if ($key['servs'] == $serv[$cont]) {
                  $control = $serv[$cont];
                }
              }
              if ($control != $serv[$cont]) {
                $servicio_g = $conexion->prepare('INSERT INTO servs_modelo (id, model, servs) VALUES (null, :model, :serv)');
                $servicio_g->execute(array(
                  ':model' => $user_id,
                  ':serv' => $serv[$cont]
                ));
                $error_s = $servicio_g->errorInfo();

                foreach ($error_s as $error_se) {
                  if(empty($error_se)){
                  }elseif ($error_se == 0) {
                  }else{
                    echo '<script language="javascript">alert(" ' . $error_se . ' ");</script>';
                  }
                }
              }
            }
          }
          if ($check == 0) {
            $uncheck = $servi['id_serv'];
            foreach ($servs_model as $key) {
              if ($key['servs'] == $uncheck) {
                $servicio_d = $conexion->prepare('DELETE FROM servs_modelo WHERE model = :model AND servs = :serv');
                $servicio_d->execute(array(
                  ':model' => $user_id,
                  ':serv' => $uncheck
                ));
                $error_d = $servicio_d->errorInfo();

                foreach ($error_d as $error_de) {
                  if(empty($error_de)){
                  }elseif ($error_de == 0) {
                  }else{
                    echo '<script language="javascript">alert(" ' . $error_de . ' ");</script>';
                  }
                }
              }
            }
          }
        }

        $cont = 0;
        foreach ($otros_servicios as $key) {
          $check = 0;
          foreach ($otros_serv as $o_serv) {
            $control = 0;
            if ($key['codigo_servi'] == $o_serv[$cont]) {
              $check = $o_serv[$cont];
              foreach ($otros_serv_model as $o_model) {
                if ($o_model['otro_servi'] == $o_serv[$cont]) {
                  $control = $o_serv[$cont];
                }
              }
              if ($control != $o_serv[$cont]) {
                $otro_s_g = $conexion->prepare('INSERT INTO otroserv_modelo (id_otro_s, models, otro_servi) VALUES (null, :model, :o_serv)');
                $otro_s_g->execute(array(
                  ':model' => $user_id,
                  ':o_serv' => $o_serv[$cont]
                ));
                $error_o_s = $otro_s_g->errorInfo();

                foreach ($error_o_s as $error_o) {
                  if(empty($error_o)){
                  }elseif ($error_o == 0) {
                  }else{
                    echo '<script language="javascript">alert(" ' . $error_o . ' ");</script>';
                  }
                }
              }
            }
          }
          if ($check == 0) {
            $uncheck = $key['codigo_servi'];
            foreach ($otros_serv_model as $otro_servicio) {
              if ($otro_servicio['otro_servi'] == $uncheck) {
                $servicio_d = $conexion->prepare('DELETE FROM otroserv_modelo WHERE models = :model AND otro_servi = :serv');
                $servicio_d->execute(array(
                  ':model' => $user_id,
                  ':serv' => $uncheck
                ));
                $error_d = $servicio_d->errorInfo();

                foreach ($error_d as $error_de) {
                  if(empty($error_de)){
                  }elseif ($error_de == 0) {
                  }else{
                    echo '<script language="javascript">alert(" ' . $error_de . ' ");</script>';
                  }
                }
              }
            }
          }
        }


        $errores = $statement->errorInfo();

        foreach ($errores as $errore) {
          if(empty($errore)){
            header('Refresh: 1; url=http://www.chicas507.com/exito.php');
          }elseif ($errore == 0) {
          }else{
            echo '<script language="javascript">alert(" ' . $errore . ' ");</script>';
          }
        }
      }else{
        $error .='<script language="javascript">alert("Datos Incorrectos!");</script>';
      }
    }

    if(isset($_POST["upload_imagen"])){
      $limite_foto = obtener_fotos_upload($conexion, $id);

      $img_save = [];
      $codigo = [];
      $c = 0;
      $img = [];

      foreach ($limite_foto as $foto) {
        $img_save[$c] = $foto['foto'];
        $codigo[$c] = $foto['id_foto'];
        $c++;
      }

      $c = 0;
      foreach($_FILES['img']['tmp_name'] as $key => $tmp_name ){

          $file_tmp = @getimagesize($_FILES['img']['tmp_name'][$key]);

        if (empty($file_tmp)) {
          $img[$c] = $img_save[$c];
        }else{

          $carpeta_destino = '../images/';
          $archivo_subido = $carpeta_destino . $key.$_FILES['img']['name'][$key];
          move_uploaded_file($_FILES['img']['tmp_name'][$key], $archivo_subido);
          $img[$c] = $key.$_FILES['img']['name'][$key];
          $guardado = $conexion->prepare('UPDATE fotos_modelo SET
            foto = :img
            WHERE id_foto = :id');

          $guardado->execute(array(
            ':img' => $img[$c],
            ':id' => $codigo[$c]
            ));
        }
        $c++;
      }
      $error = $guardado->errorInfo();
      foreach ($error as $errores) {
        if(empty($errores)){
          header('Refresh: 1; url=http://www.chicas507.com/exito.php');
          $error = '';
        }else{
          $error = '<li>' . $errores . '</li>';
        }
      }
    }

    if(isset($_POST["imagen"])){

      $c = 0;
      $img = [];

      foreach($_FILES['img']['tmp_name'] as $key => $tmp_name ){

          $file_tmp = @getimagesize($_FILES['img']['tmp_name'][$key]);
        if (!empty($file_tmp)) {

          $carpeta_destino = '../images/';
          $archivo_subido = $carpeta_destino . $key.$_FILES['img']['name'][$key];
          move_uploaded_file($_FILES['img']['tmp_name'][$key], $archivo_subido);
          $img[$c] = $key.$_FILES['img']['name'][$key];
          $guardado = $conexion->prepare('INSERT INTO fotos_modelo (id_foto, foto, modelo) VALUES (null, :foto, :id)');

          $guardado->execute(array(
            ':foto' => $img[$c],
            ':id' => $id
            ));
        }
        $c++;
      }
      $error = $guardado->errorInfo();
      foreach ($error as $errores) {
        if(empty($errores)){
          header('Refresh: 1; url=http://www.chicas507.com/exito.php');
          $error = '';
        }else{
          $error = '<li>' . $errores . '</li>';
        }
      }
    }

    if(isset($_POST["upload_video"])){
      $limite_video = obtener_video_upload($conexion, $id);
      $limite = $limite_video[0];
      $codigo = $limite['id_video'];

      extract($_POST);

      $video = '';

      $carpeta_destino = '../videos/';

      $archivo_subido = $carpeta_destino . basename($_FILES['video']['name']);

      $type = pathinfo($archivo_subido, PATHINFO_EXTENSION);

      if ($type != 'mp4' && $type != 'flv' && $type != 'avi' && $type != 'mpeg' && $type != '3gp') {
        echo '<script language="javascript">alert("Formato de Archivo No Soportado");</script>';
        header('Refresh: 1; url=' . route_admin);
      }else{
        $video = $_FILES['video']['name'];
        move_uploaded_file($_FILES['video']['tmp_name'], $archivo_subido);
        $guardado = $conexion->prepare('UPDATE video_modelo SET
          video = :video
          WHERE id_video = :id');

        $guardado->execute(array(
          ':video' => $video,
          ':id' => $codigo
          ));
        if (isset($guardado)) {
          $error = $guardado->errorInfo();
          foreach ($error as $errores) {
            if(empty($errores)){
              header('Refresh: 1; url=http://www.chicas507.com/exito.php');
              $error = '';
            }else{
              $error = '<li>' . $errores . '</li>';
            }
          }
        }
      }
    }

    if(isset($_POST["video"])){
      $limite_video = obtener_video_upload($conexion, $id);
      $user_id = $model['codigo'];
      extract($_POST);

      $video = '';

      if (empty($limite_video)) {
        $carpeta_destino = '../videos/';

        $archivo_subido = $carpeta_destino . basename($_FILES['video']['name']);

        $type = pathinfo($archivo_subido, PATHINFO_EXTENSION);

        if ($type != 'mp4' && $type != 'flv' && $type != 'avi' && $type != 'mpeg' && $type != '3gp') {
          echo '<script language="javascript">alert("Formato de Archivo No Soportado");</script>';
          header('Refresh: 1; url=' . route_admin);
        }else{
          $video = $_FILES['video']['name'];
          move_uploaded_file($_FILES['video']['tmp_name'], $archivo_subido);
          $guardado = $conexion->prepare('INSERT INTO video_modelo (id_video, video, modelo_video) VALUES (null, :video, :id)');

          $guardado->execute(array(
            ':video' => $video,
            ':id' => $user_id
            ));
          if (isset($guardado)) {
            $error = $guardado->errorInfo();
            foreach ($error as $errores) {
              if(empty($errores)){
                header('Refresh: 1; url=http://www.chicas507.com/exito.php');
                $error = '';
              }else{
                $error = '<li>' . $errores . '</li>';
              }
            }
          }
        }
      }
    }

  }

  require '../../views/admin/ver_modelo.php';
  ob_end_flush();
?>