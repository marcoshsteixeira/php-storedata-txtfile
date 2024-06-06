<?php

DEFINE('DB', 'db.txt');
DEFINE('VALMIN', '0.20');

class parkAdmin
{
    private $placa;
    private $modelo;
    private $cor;

    public function getPlaca()
    {
        return $this->placa;
    }

    public function setPlaca($placa)
    {
        $this->placa = $placa;
    }

    public function getModelo()
    {
        return $this->modelo;
    }

    public function setModelo($modelo)
    {
        $this->modelo = $modelo;
    }

    public function getCor()
    {
        return $this->cor;
    }

    public function setCor($cor)
    {
        $this->cor = $cor;
    }

    public function index()
    {
        echo '<form method="post" action="">
                Placa: <input type="text" name="placa"><br/>
                Cor: <select name="cor">
                    <option value="Azul">Azul</option>
                    <option value="Branco">Branco</option>
                    <option value="Cinza">Cinza</option>
                    <option value="Preto">Preto</option>
                    <option value="Vermelho">Vermelho</option>
                </select><br/>
                Modelo: <select name="modelo">
                    <option value="Cobalt">Cobalt</option>
                    <option value="Fiesta">Fiesta</option>
                    <option value="Gol">Gol</option>
                    <option value="Onix">Onix</option>
                    <option value="Uno">Uno</option>
                </select><br/>
                Entrada: <input type="radio" name="tipo" value="1"> Saída: <input type="radio" name="tipo" value="0">
                <br/>
                <input type="submit" value="Enviar">
            </form>';

        if ($_POST) {
            $this->setPlaca($_POST['placa']);
            $this->setCor($_POST['cor']);
            $this->setModelo($_POST['modelo']);

            if ($_POST['tipo'] == 1) {
                $retorno = $this->entrarCarro();
            } else {
                $retorno = $this->sairCarro();
            }

            echo $retorno;
        }
    }

    public function entrarCarro()
    {
        $fp = fopen(DB, "a+");

        fwrite($fp, $this->getPlaca() . ' | ' . $this->getModelo() . ' | ' . $this->getCor() . ' | 1 | ' . date("Y-m-d H:i:s") . "\n");

        fclose($fp);

        return 'Entrada realizada com sucesso.';
    }

    public function sairCarro()
    {
        $entrada = $this->getPlaca() . ' | ' . $this->getModelo() . ' | ' . $this->getCor() . ' | 1 | ' . date("Y-m-d");

        $conteudoArquivo = file_get_contents(DB);
        $pattern = preg_quote($entrada, '/');
        $pattern = "/^.*$pattern.*\$/m";

        if (preg_match_all($pattern, $conteudoArquivo, $retorno)) {

            $resultado = explode(" | ", $retorno[0][sizeof($retorno) - 1]);
            $dataAtual = date("Y-m-d H:i:s");

            $dataEntrada = new DateTime($resultado[4]);

            $dataSaida = new DateTime($dataAtual);

            $diferenca = $dataEntrada->diff($dataSaida);

            $horas = $diferenca->format('%h');
            $minutos = $diferenca->format('%i');

            $minutagemTotal = ($horas * 60) + $minutos;

            $valorPagar = VALMIN * $minutagemTotal;

            $fp = fopen(DB, "a+");

            fwrite($fp, $this->getPlaca() . ' | ' . $this->getModelo() . ' | ' . $this->getCor() . ' | 0 | ' . $dataAtual . "\n");

            fclose($fp);

            return 'Saída realizada com sucesso. O valor total a ser pago é de R$ ' . number_format($valorPagar, 2, ',', '.');
        } else {
            return 'Não foi encontrada uma entrada para esse carro em nosso estacionamento, portanto não é possível realizar a saida dele.';
        }
    }
}