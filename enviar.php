<?php
// Configurações
$destinatario = "matheusfeitoza@bateforte.com.br"; // troque para seu email
$assunto = "Novo cadastro de motorista";

// Monta corpo do e-mail
$mensagem = "Novo cadastro recebido:\n\n";
$mensagem .= "Nome: " . $_POST['nome'] . "\n";
$mensagem .= "CNH: " . $_POST['cnh'] . "\n";
$mensagem .= "CRLV: " . $_POST['crlv'] . "\n";
$mensagem .= "ANTT: " . $_POST['antt'] . "\n";
$mensagem .= "PIS: " . $_POST['pis'] . "\n";
$mensagem .= "Email: " . $_POST['email'] . "\n";

// Cabeçalhos
$boundary = md5(uniqid(time()));
$cabecalhos = "MIME-Version: 1.0\r\n";
$cabecalhos .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
$cabecalhos .= "From: Formulário <no-reply@bateforte.com.br>\r\n";

// Corpo inicial
$corpo  = "--$boundary\r\n";
$corpo .= "Content-Type: text/plain; charset=UTF-8\r\n";
$corpo .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$corpo .= $mensagem . "\r\n";

// Função para anexar arquivo
function anexarArquivo($nomeCampo, &$corpo, $boundary) {
    if (!empty($_FILES[$nomeCampo]['name'])) {
        if (is_array($_FILES[$nomeCampo]['name'])) {
            // Caso seja múltiplo (ex.: fotosVeiculo[])
            foreach ($_FILES[$nomeCampo]['name'] as $i => $nome) {
                $tmp = $_FILES[$nomeCampo]['tmp_name'][$i];
                if (file_exists($tmp)) {
                    $conteudo = chunk_split(base64_encode(file_get_contents($tmp)));
                    $corpo .= "--$boundary\r\n";
                    $corpo .= "Content-Type: application/octet-stream; name=\"$nome\"\r\n";
                    $corpo .= "Content-Disposition: attachment; filename=\"$nome\"\r\n";
                    $corpo .= "Content-Transfer-Encoding: base64\r\n\r\n";
                    $corpo .= $conteudo . "\r\n";
                }
            }
        } else {
            $tmp = $_FILES[$nomeCampo]['tmp_name'];
            $nome = $_FILES[$nomeCampo]['name'];
            if (file_exists($tmp)) {
                $conteudo = chunk_split(base64_encode(file_get_contents($tmp)));
                $corpo .= "--$boundary\r\n";
                $corpo .= "Content-Type: application/octet-stream; name=\"$nome\"\r\n";
                $corpo .= "Content-Disposition: attachment; filename=\"$nome\"\r\n";
                $corpo .= "Content-Transfer-Encoding: base64\r\n\r\n";
                $corpo .= $conteudo . "\r\n";
            }
        }
    }
}

// Anexa os arquivos
anexarArquivo("cnhArquivo", $corpo, $boundary);
anexarArquivo("comprovante", $corpo, $boundary);
anexarArquivo("fotosVeiculo", $corpo, $boundary);
anexarArquivo("uploadContrato", $corpo, $boundary);

// Fecha e-mail
$corpo .= "--$boundary--";

// Envia
if (mail($destinatario, $assunto, $corpo, $cabecalhos)) {
    echo "Cadastro enviado com sucesso!";
} else {
    echo "Erro ao enviar. Tente novamente.";
}
?>
