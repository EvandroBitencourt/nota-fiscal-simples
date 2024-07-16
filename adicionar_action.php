<?php
session_start(); // Inicia a sessão

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = 'Método de requisição não permitido.';
    header("Location: add_nota.php");
    exit; 
}

require 'config.php';

function validarCNPJ($cnpj) {
    
    return ($cnpj && $cnpj === "09.066.241/0008-84");
}

$cnpj = filter_input(INPUT_POST, 'cnpj', FILTER_SANITIZE_STRING);
if (!validarCNPJ($cnpj)) {
    $_SESSION['message'] = 'CNPJ não é válido.';
    header("Location: add_nota.php");
    exit; 
}

// Diretório onde os arquivos serão salvos
$pasta = "assets/arquivos/";

if ($_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['message'] = 'Erro ao enviar o arquivo.';
    header("Location: add_nota.php");
    exit; 
}

$nomeArquivo = $_FILES['arquivo']['name'];
$arquivoTemp = $_FILES['arquivo']['tmp_name'];

$extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
$formatosPermitidos = array("xml", "XML");
if (!in_array(strtolower($extensao), $formatosPermitidos)) {
    $_SESSION['message'] = 'Formato de arquivo não permitido. Envie um arquivo XML.';
    header("Location: add_nota.php");
    exit; 
}

if (!move_uploaded_file($arquivoTemp, $pasta . $nomeArquivo)) {
    $_SESSION['message'] = 'Erro ao mover o arquivo para a pasta de destino.';
    header("Location: add_nota.php");
    exit; 
}

// Caminho completo do arquivo XML
$xmlPath = $pasta . $nomeArquivo;

$xml = simplexml_load_file($xmlPath);
if ($xml === false) {
    $_SESSION['message'] = 'Erro ao processar o arquivo XML.';
    header("Location: add_nota.php");
    exit; 
}

$nf = (string) $xml->infNFe->ide->nNF;
$dataEmissao = (string) $xml->infNFe->ide->dhEmi;
$destinatario = (string) $xml->infNFe->dest->xNome;
$valorTotalString = (string) $xml->infNFe->total->ICMSTot->vNF;
$valorTotal = (float) str_replace(',', '.', $valorTotalString);

// Verifica se os dados extraídos do XML são válidos
if (empty($nf) || empty($dataEmissao) || empty($destinatario) || $valorTotal <= 0) {
    $_SESSION['message'] = 'Os dados do XML estão incompletos ou inválidos.';
    header("Location: add_nota.php");
    exit; 
}

$sql = $pdo->prepare("INSERT INTO notas (destinatario, num, data, valor) VALUES (:destinatario, :num, :data, :valor)");
$sql->bindValue(':destinatario', $destinatario);
$sql->bindValue(':num', $nf);
$sql->bindValue(':data', $dataEmissao);
$sql->bindValue(':valor', number_format($valorTotal, 2, '.', ''));
if ($sql->execute()) {
    $_SESSION['message'] = 'Arquivo XML enviado e processado com sucesso.';
} else {
    $_SESSION['message'] = 'Erro ao inserir os dados no banco de dados.';
}

header("Location: add_nota.php");
exit; 
?>
