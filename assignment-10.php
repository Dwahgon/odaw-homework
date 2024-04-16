<!DOCTYPE html>
<html>
    <head>
        <title>Exercício 10</title>
    </head>
    <style>
        .error {
            background-color: red;
        }

        .success {
            background-color: green;
        }
    </style>
    <body>
        <section>
            <h1>Exercicio 10 - Questão 1</h1>
            <p>
            Criar um formulário com campos variados, ex.: texto, senha, área de texto, select, checkbox, radio, botões (submit e reset), etc.</br>
            a - Mostrar mensagem de erro até que todos os campos sejam preenchidos corretamente, com validação em PHP;</br>
            b - Escolha 2 campos para validar o conteúdo digitado com PHP. Ex. e-mail, data, senha, etc.</br>
            c - Mostrar mensagem de confirmação com as informações digitadas/escolhidas pelo usuário.
            </p>


            <?php
                $errs = array();
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit-form-1'])) {
                    $name = trim($_POST["name"]);
                    $email = trim($_POST["email"]);
                    $emailConfirm = trim($_POST["email-confirm"]);
                    $birthday = trim($_POST["birthday"]);
                    $password = trim($_POST["password"]);
                    $passwordConfirm = trim($_POST["password-confirm"]);

                    // Check for empty fields (a)
                    if(empty($name)) array_push($errs, "Campo nome não foi preenchido");
                    if(empty($email)) array_push($errs, "Campo email não foi preenchido");
                    if(empty($emailConfirm)) array_push($errs, "Campo confirmar email não foi preenchido");
                    if(empty($birthday)) array_push($errs, "Campo data de nascimento não foi preenchido");
                    if(empty($password)) array_push($errs, "Campo senha não foi preenchido");
                    if(empty($passwordConfirm)) array_push($errs, "Campo confirmar senha não foi preenchido");

                    // Validate field contents (b)
                    if($email != $emailConfirm) array_push($errs, "Campo e-mail e confirmar e-mail são diferentes");
                    if($password != $passwordConfirm) array_push($errs, "Campo senha e confirmar senha são diferentes");
                    if(preg_match("/^[\w\-\.]+@([\w\-]+\.)+[\w\-]{2,4}$/", $email) != 1) array_push($errs, "E-mail inválido");
                    if((new DateTime($birthday))->diff(new DateTime())->y < 13) array_push($errs, "Só é permitido maiores de 13 anos");
                    if(strlen($password) < 8 || preg_match("/[0-9]/", $password) != 1 || preg_match("/[a-z]/", $password) != 1 || preg_match("/[A-Z]/", $password) != 1) array_push($errs, "A senha deve conter no mínimo 8 caracteres, com números, letras minúsculas e letras maiúsculas");

                    ?>
                        <div class="<?= empty($errs) ? 'success': 'error' ?>">
                            <?php
                                if(empty($errs)){
                            ?>
                                <p>Informações cadastrados com sucesso:</p>
                                <ul>
                                    <li>Nome: <?= $name ?></li>
                                    <li>Email: <?= $email ?></li>
                                    <li>Data de Nascimento: <?= $birthday ?></li>
                                    <li>Senha: <?= $password ?></li>
                                </ul>
                                
                            <?php
                                }else{
                                    echo implode("</br>", $errs);
                                }
                            ?>
                        </div>
                    <?php
                }
            ?>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <table>
                    <tbody>
                        <tr>
                            <td><label for="name">Nome:</label></td>
                            <td><input id="name" type="text" name="name"></td>
                        </tr>
                        <tr>
                            <td><label for="email">Email:</label></td>
                            <td><input id="email" type="text" name="email"></td>
                        </tr>
                        <tr>
                            <td><label for="email-confirm">Confirmar Email:</label></td>
                            <td><input id="email-confirm" type="text" name="email-confirm"></td>
                        </tr>
                        <tr>
                            <td><label for="birthday">Data de Nascimento:</label></td>
                            <td><input id="birthday" type="date" name="birthday"></td>
                        </tr>
                        <tr>
                            <td><label for="password">Senha:</label></td>
                            <td><input id="password" type="password" name="password"></td>
                        </tr>
                        <tr>
                            <td><label for="password-confirm">Confirmar Senha:</label></td>
                            <td><input id="password-confirm" type="password" name="password-confirm"></td>
                        </tr>
                        <tr>
                            <td><input type="submit" name="submit-form-1"/></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </section>
        <section>
            <h1>Exercicio 10 - Questão 2 e 3</h1>
            <p>2 - Validar usuário e senha (login) com as informações de um arquivo 'autenticacao.txt'.</p>
            <p>3 - Pesquise e teste uma forma de cifrar e validar uma senha/informação qualquer.</p>

            <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit-form-signup'])) {
                    $file = fopen("autenticacao.txt", "a");
                    $user = $_POST["user"];
                    $password = hash("sha256", $_POST["password"]);
                    fwrite($file, "$user\n$password\n\n");
                    fclose($file);
                    ?>
                        <span class="success">
                            Usuário cadastrado com sucesso
                        </span>
                    <?php
                }

                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit-form-login'])) {
                    $loginuser = $_POST["user"];
                    $loginpassword = hash("sha256", $_POST["password"]);
                    $success = false;
                    if (filesize("autenticacao.txt") > 0){
                        $file = fopen("autenticacao.txt", "r");
                        $fileStr = fread($file, filesize("autenticacao.txt"));
                        $userRegs = array_filter(explode("\n\n", $fileStr), function($s){ return strlen($s) != 0; });

                        foreach ($userRegs as $userReg) {
                            [$user, $pw] = explode("\n", $userReg);
                            if (($loginuser == $user) && ($loginpassword == $pw)){
                                $success = true;
                                break;
                            }
                        }
                        fclose($file);
                    }
                    ?>
                        <span class="<?= $success ? "success" : "error" ?>">
                            <?= $success ? "Acesso autorizado" : "Acesso negado" ?>
                        </span>
                    <?php
                }
            ?>

            <div style="display: flex">

                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <h2>Cadastrar:</h2>
                    <table>
                        <tbody>
                            <tr>
                                <td><label for="user">Usuário:</label></td>
                                <td><input id="user" type="text" name="user"></td>
                            </tr>
                            <tr>
                                <td><label for="password">Senha:</label></td>
                                <td><input id="password" type="password" name="password"></td>
                            </tr>
                            <td>
                                <td><input type="submit" name="submit-form-signup"/></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <h2>Validar:</h2>
                <table>
                    <tbody>
                        <tr>
                            <td><label for="user">Usuário:</label></td>
                            <td><input id="user" type="text" name="user"></td>
                        </tr>
                        <tr>
                            <td><label for="password">Senha:</label></td>
                            <td><input id="password" type="password" name="password"></td>
                        </tr>
                        <td>
                            <td><input type="submit" name="submit-form-login"/></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </section>
    </body>
</html>