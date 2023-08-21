<?php include "../inc/dbinfo.inc"; ?>
<html>

<body>
    <h1>Empregados</h1>
    <?php

    /* Conectar ao MySQL e selecionar o banco de dados. */
    $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

    if (mysqli_connect_errno())
        echo "Falha ao conectar ao MySQL: " . mysqli_connect_error();

    $database = mysqli_select_db($connection, DB_DATABASE);

    /* Garantir que a tabela EMPREGADOS exista. */
    VerificarTabelaEmpregados($connection, DB_DATABASE);

    /* Se os campos de entrada estiverem preenchidos, adicionar uma linha à tabela EMPREGADOS. */
    $employee_name = htmlentities($_POST['NAME']);
    $employee_address = htmlentities($_POST['ADDRESS']);
    $employee_birthday = htmlentities($_POST['BIRTHDAY']);
    $employee_salary = htmlentities($_POST['SALARY']);

    if (strlen($employee_name) || strlen($employee_address) || strlen($employee_birthday) || strlen($employee_salary)) {
        AdicionarEmpregado($connection, $employee_name, $employee_address, $employee_birthday, $employee_salary);
    }
    ?>

    <!-- Formulário de entrada -->
    <form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
        <table border="0">
            <tr>
                <td>NOME</td>
                <td>ENDEREÇO</td>
                <td>DATA DE NASCIMENTO</td>
                <td>SALARIO</td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="NAME" maxlength="45" size="30" />
                </td>
                <td>
                    <input type="text" name="ADDRESS" maxlength="90" size="60" />
                </td>
                <td>
                    <input type="date" name="BIRTHDAY" />
                </td>
                <td>
                    <input type="number" name="SALARY" />
                </td>
                <td>
                    <input type="submit" value="Adicionar Dados" />
                </td>
            </tr>
        </table>
    </form>

    <!-- Exibir dados da tabela. -->
    <table border="1" cellpadding="2" cellspacing="2">
        <tr>
            <td>ID</td>
            <td>NOME</td>
            <td>ENDEREÇO</td>
            <td>DATA DE NASCIMENTO</td>
            <td>SALARIO</td>
        </tr>

        <?php

        $result = mysqli_query($connection, "SELECT * FROM EMPREGADOS");

        while ($query_data = mysqli_fetch_row($result)) {
            echo "<tr>";
            echo "<td>", $query_data[0], "</td>",
                "<td>", $query_data[1], "</td>",
                "<td>", $query_data[2], "</td>",
                "<td>", $query_data[3], "</td>",
                "<td>", $query_data[4], "</td>";
            echo "</tr>";
        }
        ?>

    </table>

    <!-- Limpeza. -->
    <?php

    mysqli_free_result($result);
    mysqli_close($connection);

    ?>

</body>

</html>


<?php

/* Adicionar um empregado à tabela. */
function AdicionarEmpregado($connection, $nome, $endereco, $aniversario, $salario)
{
    $n = mysqli_real_escape_string($connection, $nome);
    $a = mysqli_real_escape_string($connection, $endereco);
    $b = mysqli_real_escape_string($connection, $aniversario);
    $s = mysqli_real_escape_string($connection, $salario);

    $query = "INSERT INTO EMPREGADOS (NAME, ADDRESS, BIRTHDAY, SALARY) VALUES ('$n', '$a', '$b', '$s');";

    if (!mysqli_query($connection, $query))
        echo ("<p>Erro ao adicionar dados do empregado.</p>");
}

/* Verificar se a tabela existe e, caso contrário, criá-la. */
function VerificarTabelaEmpregados($connection, $dbName)
{
    if (!TabelaExiste("EMPREGADOS", $connection, $dbName)) {
        $query = "CREATE TABLE EMPREGADOS (
         ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         NAME VARCHAR(45),
         ADDRESS VARCHAR(90),
	 BIRTHDAY DATE, 
	 SALARY DECIMAL(5,2)
       )";

        if (!mysqli_query($connection, $query))
            echo ("<p>Erro ao criar tabela.</p>");
    }
}

/* Verificar a existência de uma tabela. */
function TabelaExiste($nomeTabela, $connection, $dbName)
{
    $t = mysqli_real_escape_string($connection, $nomeTabela);
    $d = mysqli_real_escape_string($connection, $dbName);

    $verificarTabela = mysqli_query(
        $connection,
        "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'"
    );

    if (mysqli_num_rows($verificarTabela) > 0)
        return true;

    return false;
}
?>