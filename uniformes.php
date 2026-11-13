<?php
include('conexion.php');

// ACCIÓN PARA AGREGAR AL CARRITO EN LA BASE DE DATOS
if (isset($_POST['agregar'])) {
    if (!isset($_SESSION['usuario_id'])) {
        echo "<script>alert('Debes iniciar sesión para añadir productos al carrito.'); window.location.href='login.html';</script>";
        exit();
    }

    $usuario_id  = $_SESSION['usuario_id'];
    $producto_id = $_POST['id_producto'];

    // Verificar si ya existe este producto en el carrito del usuario
    $check = $conexion->prepare("SELECT id, cantidad FROM carrito WHERE usuario_id = ? AND producto_id = ?");
    $check->bind_param("ii", $usuario_id, $producto_id);
    $check->execute();
    $res = $check->get_result();

    if ($row = $res->fetch_assoc()) {
        $nueva_cant = $row['cantidad'] + 1;
        $update = $conexion->prepare("UPDATE carrito SET cantidad = ? WHERE id = ?");
        $update->bind_param("ii", $nueva_cant, $row['id']);
        $update->execute();
        $update->close();
    } else {
        $insert = $conexion->prepare("INSERT INTO carrito (usuario_id, producto_id, cantidad) VALUES (?, ?, 1)");
        $insert->bind_param("ii", $usuario_id, $producto_id);
        $insert->execute();
        $insert->close();
    }
    $check->close();

    echo "<script>alert('¡Jersey guardado en tu carrito!'); window.location.href='uniformes.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Uniformes NBA - Basketball Store</title>
</head>
<body bgcolor="#f4f4f4" text="#000000" link="#0000ee" vlink="#551a8b" style="margin: 0; padding: 0;">

    <!-- HEADER / ENCABEZADO -->
    <table width="100%" bgcolor="#1a1a1a" cellpadding="15" cellspacing="0">
        <tr>
            <td align="center">
                <font color="#ffffff" size="6" face="Arial"><b>BASKETBALL STORE</b></font><br>
                <font color="#ff6600" size="3" face="Arial"><i>La tienda oficial de los apasionados del baloncesto</i></font>
            </td>
        </tr>
    </table>

    <!-- BARRA DE NAVEGACIÓN -->
    <table width="100%" bgcolor="#333333" cellpadding="12" cellspacing="0">
        <tr>
            <td align="center">
                <font face="Arial" size="4">
                    <a href="index.html"><font color="#ffffff"><b>INICIO</b></font></a> &nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="tenis.php"><font color="#ffffff"><b>TENIS</b></font></a> &nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="balones.php"><font color="#ffffff"><b>BALONES</b></font></a> &nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="uniformes.php"><font color="#ff6600"><b>UNIFORMES NBA</b></font></a> &nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="carrito.php"><font color="#ffffff"><b>CARRITO</b></font></a> &nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="login.html"><font color="#ffffff"><b>INICIAR SESIÓN</b></font></a> &nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="registro.html"><font color="#ffffff"><b>REGISTRARSE</b></font></a>
                </font>
            </td>
        </tr>
    </table>

    <br>
    <center>
        <font face="Arial" size="5" color="#1a1a1a"><h2>JERSEYS Y UNIFORMES NBA</h2></font>
        <font face="Arial" size="3" color="#555555"><p>Ediciones oficiales Nike Swingman de tus estrellas favoritas</p></font>
    </center>

    <!-- TABLA DE PRODUCTOS (UNIFORMES DESDE MYSQL) -->
    <table align="center" width="90%" border="1" cellspacing="10" cellpadding="10" bordercolor="#cccccc" bgcolor="#ffffff">
        <tr align="center">
        <?php
        $sql = "SELECT * FROM productos WHERE categoria = 'uniformes'";
        $resultado = $conexion->query($sql);
        $contador = 0;

        while ($producto = $resultado->fetch_assoc()):
            if ($contador > 0 && $contador % 5 == 0) {
                echo '</tr><tr align="center">';
            }
        ?>
            <td width="20%">
                <img src="<?php echo $producto['imagen_url']; ?>" width="160" height="130" alt="Jersey"><br><br>
                <font face="Arial" size="3"><b><?php echo $producto['nombre']; ?></b></font><br>
                <font face="Arial" size="2" color="#666666"><?php echo $producto['descripcion']; ?></font><br>
                <font face="Arial" size="4" color="#ff6600"><b>$<?php echo number_format($producto['precio'], 2); ?> MXN</b></font><br><br>
                
                <form action="uniformes.php" method="POST">
                    <input type="hidden" name="id_producto" value="<?php echo $producto['id']; ?>">
                    <button type="submit" name="agregar" style="background-color: #1a1a1a; color: white; border: none; padding: 8px 12px; font-weight: bold; cursor: pointer;">Añadir al Carrito</button>
                </form>
            </td>
        <?php 
            $contador++;
        endwhile; 
        ?>
        </tr>
    </table>

    <br><br><br>
    <!-- FOOTER -->
    <table width="100%" bgcolor="#1a1a1a" cellpadding="15" cellspacing="0" style="position: fixed; bottom: 0; left: 0;">
        <tr>
            <td align="center">
                <font color="#ffffff" size="3" face="Arial">Jaime Omar Morales Reynoso 5-J</font>
            </td>
        </tr>
    </table>

</body>
</html>