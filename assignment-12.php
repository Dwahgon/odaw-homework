<?php
    $host = "172.17.0.2";
    $user = "root";
    $password = "odaw";
    $db = "assignment12";

    $connection = mysqli_connect($host, $user, $password);
    if(!$connection){
        die("Could not connect to database:".mysqli_error());
    }
    mysqli_select_db ($connection, "assignment12");
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Exercício 12</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    </head>
    <style>
        .error {
            background-color: red;
        }

        .success {
            background-color: green;
        }

        table, td, th {
            border: 1px solid black;
            border-collapse: collapse;
        }
        td, th {
            padding: 5px;
        }
    </style>
    <body>
        <section>
            <?php
                $errs = array();
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    if (isset($_POST["delete"])){
                        $delemail = $_POST["email"];
                        $userquery = "DELETE from users where email='$delemail';";
                        $deletequeryres = mysqli_query($connection, $userquery);
                        if($deletequeryres){
                            ?>
                                <div class="success">
                                    Apagado com sucesso!
                                </div>
                            <?php
                        }
                    }else{
                        $name = trim($_POST["name"]);
                        $email = trim($_POST["email"]);
                        $birthday = trim($_POST["birthday"]);
                        $password = trim($_POST["password"]);
                        $favorite_food = trim($_POST["favorite-food"]);
    
                        // Check for empty fields (a)
                        if(empty($name)) array_push($errs, "Campo nome não foi preenchido");
                        if(empty($email)) array_push($errs, "Campo email não foi preenchido");
                        if(empty($birthday)) array_push($errs, "Campo data de nascimento não foi preenchido");
                        if(empty($password)) array_push($errs, "Campo senha não foi preenchido");
                        if(empty($favorite_food)) array_push($errs, "Campo comida favorita não foi preenchido");
    
                        // Validate field contents (b)
                        if(preg_match("/^[\w\-\.]+@([\w\-]+\.)+[\w\-]{2,4}$/", $email) != 1) array_push($errs, "E-mail inválido");
                        if((new DateTime($birthday))->diff(new DateTime())->y < 13) array_push($errs, "Só é permitido maiores de 13 anos");
                        if(strlen($password) < 8 || preg_match("/[0-9]/", $password) != 1 || preg_match("/[a-z]/", $password) != 1 || preg_match("/[A-Z]/", $password) != 1) array_push($errs, "A senha deve conter no mínimo 8 caracteres, com números, letras minúsculas e letras maiúsculas");
    
                        $hashed_password = hash("sha256", $password);

                        if (empty($errs)){
                            $insertquery = "insert into users values ('$name', '$email', '$hashed_password', '$birthday', '$favorite_food')";
                            $updatequery = "UPDATE users SET name='$name', birthday='$birthday', favorite_food='$favorite_food' WHERE email='$email' and password='$hashed_password'";
                            echo  isset($_POST["add"]) ? $insertquery : $updatequery;
                            try{
                                $res = mysqli_query($connection, isset($_POST["add"]) ? $insertquery : $updatequery);
                            }catch(mysqli_sql_exception $ex){
                                echo $ex;
                                $res = null;
                            }
                            if (!$res) array_push($errs, "Erro ao salvar no banco de dados");
                        }

                        ?>
                            <div class="<?= empty($errs) ? 'success': 'error' ?>">
                                <?php
                                    if(empty($errs)){
                                ?>
                                    <p>Informações cadastrados com sucesso</p>
                                <?php
                                    }else{
                                        echo implode("</br>", $errs);
                                    }
                                    ?>
                            </div>
                        <?php
                    }

                }
            ?>
        </section>
        <section>
            <h1>Usuários cadastrados</h1>
            <form id="create-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"></form>
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Senha</th>
                        <th>Data de Nascimento</th>
                        <th>Comida Favorita</th>
                        <th colspan="2">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $userquery = "SELECT name, email, birthday, favorite_food FROM users;";
                        $userqueryres = mysqli_query($connection, $userquery);
                        $row = mysqli_fetch_row($userqueryres);

                        if (!$row){
                            ?>
                                <tr><td colspan="6" style="text-align: center;">Nenhum usuário cadastrado</td></tr>
                            <?php
                        }else{
                            do{
                                ?>
                                    <form id="row-form-<?= $row[1]?>" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"></form>
                                    <form id="delete-<?= $row[1]?>" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"></form>
                                    <tr class="user-row">
                                        <input form="delete-<?= $row[1]?>" type="hidden" name="email" value="<?= $row[1]?>" />
                                        <td><input form="row-form-<?= $row[1]?>" required type="text" name="name" value="<?= $row[0]?>" /></td>
                                        <td><input form="row-form-<?= $row[1]?>" required type="email" name="email" value="<?= $row[1]?>" /></td>
                                        <td><input form="row-form-<?= $row[1]?>" required type="password" name="password" value="" /></td>
                                        <td><input form="row-form-<?= $row[1]?>" required type="date" name="birthday" value="<?= $row[2]?>" /></td>
                                        <td><select form="row-form-<?= $row[1]?>" required name="favorite-food">
                                            <option <?= $row[3] == "hamburguer" ? 'selected' : '' ?> value="hamburguer">Hamburguer</option>
                                            <option <?= $row[3] == "fried-chicken" ? 'selected' : '' ?> value="fried-chicken">Frango frito</option>
                                            <option <?= $row[3] == "french-fries" ? 'selected' : '' ?> value="french-fries">Batata frita</option>
                                            <option <?= $row[3] == "beans" ? 'selected' : '' ?> value="beans">Feijoada</option>
                                            <option <?= $row[3] == "other" ? 'selected' : '' ?> value="other">Outro</option>
                                        </select></td>
                                        <td class="buttons">
                                            <button form="row-form-<?= $row[1]?>" name="edit" class="material-symbols-outlined" type="submit" >save</button>
                                            <button form="delete-<?= $row[1]?>" name="delete" class="material-symbols-outlined" type="submit">delete</button>
                                        </td>
                                    </tr>
                                <?php
                            }while($row = mysqli_fetch_row($userqueryres));
                        }
                    ?>
                    <tr>
                        <td><input form="create-form" type="text" name="name" required/></td>
                        <td><input form="create-form" type="email" name="email" required/></td>
                        <td><input form="create-form" type="password" name="password" required/></td>
                        <td><input form="create-form" type="date" name="birthday" required/></td>
                        <td><select form="create-form" name="favorite-food" style="width: 100%" required>
                            <option selected value=""></option>
                            <option value="hamburguer">Hamburguer</option>
                            <option value="fried-chicken">Frango frito</option>
                            <option value="french-fries">Batata frita</option>
                            <option value="beans">Feijoada</option>
                            <option value="other">Outro</option>
                        </select></td>
                        <td colspan="2"><button form="create-form" name="add" class="material-symbols-outlined" type="submit">add</button></td>
                    </tr>
                </tbody>
            <table>
        </section>
    </body>
</html>
<?php mysqli_close($connection);?>