<?php
 session_start();
require 'header.php';
require 'config.php';

?>

<section class="nota">
    <div class="container">
        <i class="fas fa-upload"></i>
        <h2>Upload de XML</h2>

        <form method="POST" action="adicionar_action.php" enctype="multipart/form-data">
            <div class="input-container">
                <label for="cnpj">CNPJ:</label>
                <?php
               
                // Verifica se há uma mensagem definida na sessão
                if (isset($_SESSION['message'])) {
                    echo '<div class="alert" style="color: #D8000C;">' . $_SESSION['message'] . '</div>';
                    unset($_SESSION['message']);
                }
                ?>
                <br> <!-- Quebra de linha adicionada -->
                <input type="text" id="cnpj" name="cnpj" placeholder="00.000.000/0000-00" required>
            </div>

            <div class="input-container">
                <input type="file" id="arquivo" name="arquivo" accept=".xml" required>
                <label for="arquivo" class="upload-icon">Selecionar arquivo XML</label>
            </div>

            <div class="button-container">
                <a href="http://localhost/teste/" class="cancel-btn">Cancelar</a>
                <input type="submit" class="submit-btn" value="Enviar"/>

            </div>
        </form>
    </div>
</section>

<?php
require 'footer.php';

?>