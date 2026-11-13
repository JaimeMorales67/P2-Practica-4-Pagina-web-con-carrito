<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('conexion.php');

// ELIMINAR UN PRODUCTO DEL CARRITO EN LA BASE DE DATOS
if (isset($_POST['eliminar_carrito_id'])) {
    $carrito_id = intval($_POST['eliminar_carrito_id']);
    $usuario_id = intval($_SESSION['usuario_id']);

    $stmt = $conexion->prepare("DELETE FROM carrito WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $carrito_id, $usuario_id);
    $stmt->execute();
    $stmt->close();

    header("Location: carrito.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de Compras - Basketball Store</title>
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
                    <a href="uniformes.php"><font color="#ffffff"><b>UNIFORMES NBA</b></font></a> &nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="carrito.php"><font color="#ff6600"><b>CARRITO</b></font></a> &nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="login.html"><font color="#ffffff"><b>INICIAR SESIÓN</b></font></a> &nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="registro.html"><font color="#ffffff"><b>REGISTRARSE</b></font></a>
                </font>
            </td>
        </tr>
    </table>

    <br><br>

    <?php if (!isset($_SESSION['usuario_id'])): ?>

        <!-- USUARIO NO AUTENTICADO -->
        <table align="center" width="70%" border="1" cellspacing="0" cellpadding="30" bordercolor="#cccccc" bgcolor="#ffffff">
            <tr>
                <td align="center">
                    <font face="Arial" size="6" color="#1a1a1a"><h2>¡AÚN NO HAS INICIADO SESIÓN! 🔒</h2></font>
                    <font face="Arial" size="3" color="#555555">
                        <p>Para poder agregar productos a tu carrito de compras y guardarlos en la base de datos necesitas una cuenta activa.</p>
                        <p>Regístrate o inicia sesión para empezar a comprar.</p>
                    </font>
                    <br><br>
                    <a href="registro.html" style="background-color: #ff6600; color: white; padding: 15px 30px; text-decoration: none; font-family: Arial; font-size: 16px; font-weight: bold; display: inline-block;">REGÍSTRATE O INICIA SESIÓN PARA EMPEZAR A COMPRAR</a>
                </td>
            </tr>
        </table>

    <?php 
    else: 
        // CONSULTAR PRODUCTOS DEL CARRITO DEL USUARIO ACTUAL EN MYSQL
        $uid = intval($_SESSION['usuario_id']);
        $sql = "SELECT c.id AS carrito_id, c.cantidad, p.nombre, p.precio, p.imagen_url 
                FROM carrito c 
                INNER JOIN productos p ON c.producto_id = p.id 
                WHERE c.usuario_id = '$uid'";
        $res = $conexion->query($sql);

        if (!$res || $res->num_rows == 0):
    ?>
        <!-- CARRITO VACÍO EN MYSQL -->
        <table align="center" width="70%" border="1" cellspacing="0" cellpadding="30" bordercolor="#cccccc" bgcolor="#ffffff">
            <tr>
                <td align="center">
                    <font face="Arial" size="6" color="#1a1a1a"><h2>TU CARRITO ESTÁ VACÍO 🛒</h2></font>
                    <font face="Arial" size="3" color="#555555">
                        <p>¡Hola <b><?php echo strtoupper(htmlspecialchars($_SESSION['usuario_nombre'])); ?></b>! No tienes productos guardados en tu carrito actualmente.</p>
                    </font>
                    <br><br>
                    <a href="tenis.php" style="background-color: #ff6600; color: white; padding: 15px 30px; text-decoration: none; font-family: Arial; font-size: 16px; font-weight: bold; display: inline-block;">EMPEZAR A COMPRAR</a>
                </td>
            </tr>
        </table>

    <?php else: ?>

        <!-- CARRITO CON PRODUCTOS CONSULTADOS DE MYSQL -->
        <center>
            <font face="Arial" size="5" color="#1a1a1a"><h2>CARRITO DE COMPRAS DE <?php echo strtoupper(htmlspecialchars($_SESSION['usuario_nombre'])); ?></h2></font>
        </center><br>

        <table align="center" width="80%" border="1" cellspacing="0" cellpadding="12" bordercolor="#cccccc" bgcolor="#ffffff">
            <tr bgcolor="#1a1a1a" align="center">
                <th><font face="Arial" color="#ffffff">Producto</font></th>
                <th><font face="Arial" color="#ffffff">Precio Unitario</font></th>
                <th><font face="Arial" color="#ffffff">Cantidad</font></th>
                <th><font face="Arial" color="#ffffff">Subtotal</font></th>
                <th><font face="Arial" color="#ffffff">Acción</font></th>
            </tr>

            <?php 
            $total = 0;
            while ($item = $res->fetch_assoc()): 
                $subtotal = $item['precio'] * $item['cantidad'];
                $total += $subtotal;
            ?>
            <tr align="center">
                <td>
                    <img src="<?php echo $item['imagen_url']; ?>" width="80" height="65"><br>
                    <font face="Arial" size="2"><b><?php echo htmlspecialchars($item['nombre']); ?></b></font>
                </td>
                <td><font face="Arial" size="3">$<?php echo number_format($item['precio'], 2); ?> MXN</font></td>
                <td><font face="Arial" size="3"><?php echo $item['cantidad']; ?></font></td>
                <td><font face="Arial" size="3" color="#ff6600"><b>$<?php echo number_format($subtotal, 2); ?> MXN</b></font></td>
                <td>
                    <form action="carrito.php" method="POST">
                        <input type="hidden" name="eliminar_carrito_id" value="<?php echo $item['carrito_id']; ?>">
                        <button type="submit" style="background-color: #cc0000; color: white; border: none; padding: 5px 10px; cursor: pointer;">Eliminar</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>

            <tr bgcolor="#f9f9f9">
                <td colspan="3" align="right"><font face="Arial" size="4"><b>TOTAL DE LA COMPRA:</b></font></td>
                <td align="center"><font face="Arial" size="4" color="#ff6600"><b>$<?php echo number_format($total, 2); ?> MXN</b></font></td>
                <td align="center">--</td>
            </tr>
        </table>

        <br>
        <table align="center" cellpadding="10">
            <tr>
                <td>
                    <a href="tenis.php" style="background-color: #555555; color: white; padding: 10px 20px; text-decoration: none; font-family: Arial; font-weight: bold;">SEGUIR COMPRANDO</a>
                </td>
                <td>
                    <!-- BOTÓN QUE MUESTRA EL FORMULARIO -->
                    <button type="button" onclick="document.getElementById('formulario-pedido').style.display='block';" style="background-color: #ff6600; color: white; border: none; padding: 12px 25px; font-family: Arial; font-size: 14px; font-weight: bold; cursor: pointer;">REALIZAR PEDIDO</button>
                </td>
            </tr>
        </table>

        <br>

        <!-- FORMULARIO DE ENVÍO Y PAGO (SOLO DISEÑO, OCULTO INICIALMENTE) -->
        <div id="formulario-pedido" style="display: none;">
            <table align="center" width="450" border="1" cellspacing="0" cellpadding="20" bordercolor="#cccccc" bgcolor="#ffffff">
                <tr>
                    <td align="center">
                        <font face="Arial" size="5" color="#1a1a1a"><h3>DATOS DE ENVÍO Y PAGO</h3></font>
                        <form onsubmit="event.preventDefault(); alert('¡Formulario listo! (Sin conexión a BD por ahora)');">
                            <table width="100%" cellpadding="8" border="0">
                                <tr>
                                    <td>
                                        <font face="Arial" size="3"><b>Colonia:</b></font><br>
                                        <input type="text" name="colonia" placeholder="Ej. Centro" required style="width: 95%; padding: 6px;">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <font face="Arial" size="3"><b>Código Postal:</b></font><br>
                                        <input type="text" name="codigo_postal" placeholder="Ej. 32000" required style="width: 95%; padding: 6px;">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <font face="Arial" size="3"><b>Ciudad:</b></font><br>
                                        <input type="text" name="ciudad" placeholder="Ej. Ciudad Juárez" required style="width: 95%; padding: 6px;">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <font face="Arial" size="3"><b>Número de Tarjeta:</b></font><br>
                                        <input type="text" name="num_tarjeta" placeholder="XXXX-XXXX-XXXX-XXXX" maxlength="19" required style="width: 95%; padding: 6px;">
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center"><br>
                                        <button type="submit" style="background-color: #1a1a1a; color: white; border: none; padding: 10px 25px; font-family: Arial; font-weight: bold; cursor: pointer;">CONFIRMAR COMPRA</button>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </td>
                </tr>
            </table>
        </div>

    <?php 
        endif;
    endif; 
    ?>

    <br><br><br><br>
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