<?php include "../inc/dbinfo.inc"; ?>
<html>

<body>
    <h1>Empregados</h1>
    <?php

    /* Connect to MySQL and select the database. */
    $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

    if (mysqli_connect_errno())
        echo "Failed to connect to MySQL: " . mysqli_connect_error();

    $database = mysqli_select_db($connection, DB_DATABASE);

    /* Ensure that the EMPLOYEES table exists. */
    VerifyEmployeesTable($connection, DB_DATABASE);

    /* If input fields are populated, add a row to the EMPLOYEES table. */
    $employee_name = htmlentities($_POST['NAME']);
    $employee_address = htmlentities($_POST['ADDRESS']);
    $employee_birthday = htmlentities($_POST['BIRTHDAY']);
    $employee_salary = htmlentities($_POST['SALARY']);

    if (strlen($employee_name) || strlen($employee_address) || strlen($employee_birthday) || strlen($employee_salary)) {
        AddEmployee($connection, $employee_name, $employee_address, $employee_birthday, $employee_salary);
    }
    ?>

    <!-- Input form -->
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
                    <input type="submit" value="Add Data" />
                </td>
            </tr>
        </table>
    </form>

    <!-- Display table data. -->
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

    <!-- Clean up. -->
    <?php

    mysqli_free_result($result);
    mysqli_close($connection);

    ?>

</body>

</html>


<?php

/* Add an employee to the table. */
function AddEmployee($connection, $name, $address, $birthday, $salary)
{
    $n = mysqli_real_escape_string($connection, $name);
    $a = mysqli_real_escape_string($connection, $address);
    $b = mysqli_real_escape_string($connection, $birthday);
    $s = mysqli_real_escape_string($connection, $salary);

    $query = "INSERT INTO EMPREGADOS (NAME, ADDRESS, BIRTHDAY, SALARY) VALUES ('$n', '$a', '$b', '$s');";

    if (!mysqli_query($connection, $query))
        echo ("<p>Error adding employee data.</p>");
}

/* Check whether the table exists and, if not, create it. */
function VerifyEmployeesTable($connection, $dbName)
{
    if (!TableExists("EMPREGADOS", $connection, $dbName)) {
        $query = "CREATE TABLE EMPREGADOS (
         ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         NAME VARCHAR(45),
         ADDRESS VARCHAR(90),
	BIRTHDAY DATE, 
	SALARY DECIMAL(5,2)
       )";

        if (!mysqli_query($connection, $query))
            echo ("<p>Error creating table.</p>");
    }
}

/* Check for the existence of a table. */
function TableExists($tableName, $connection, $dbName)
{
    $t = mysqli_real_escape_string($connection, $tableName);
    $d = mysqli_real_escape_string($connection, $dbName);

    $checktable = mysqli_query(
        $connection,
        "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'"
    );

    if (mysqli_num_rows($checktable) > 0)
        return true;

    return false;
}
?>