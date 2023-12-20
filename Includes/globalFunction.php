<?php

//
function is_json($data)
{
     return json_decode($data);
}

//
function jsonList($data)
{
     $temp = json_encode($data);
     return json_decode($temp);
}

//
function jsonUpdt($json1, $json2)
{
     //$json2 = json_decode($json2);

     foreach ($json1 as $key => $value) {
          if (isset($json2->$key))
               $json1->$key = $json2->$key;
     }

     return $json1;
}

function validarJSON($json) {
     json_decode($json);
     return (json_last_error() === JSON_ERROR_NONE);
}

function isJson($temp){      
     if(validarJSON($temp))
          $temp = json_decode($temp);

    return $temp;
}
//
function unirJson($json1, $json2)
{
     // Decodificar os JSON em arrays associativos
     $array1 = json_decode($json1, true);
     $array2 = json_decode($json2, true);

     if ($array1 === null || $array2 === null) {
          // Verificar se a decodificação falhou
          return false;
     }

     // Mesclar os arrays
     $resultado = array_merge_recursive($array1, $array2);

     // Codificar o resultado de volta para JSON
     $jsonResultado = json_encode($resultado);

     if ($jsonResultado === false) {
          // Verificar se a codificação falhou
          return false;
     }

     return $jsonResultado;
}

function upArray(&$array1, $array2)
{
     foreach ($array2 as $key => $value) {
          if (array_key_exists($key, $array1)) {
               if ($array1[$key] !== $value) {
                    $array1[$key] = $value;
               }
          } else {
               $array1[$key] = $value;
          }
     }
}

//
function formatCnpjCpf($value)
{
     $CPF_LENGTH = 11;
     $cnpj_cpf = preg_replace("/\D/", '', $value);

     if (strlen($cnpj_cpf) === $CPF_LENGTH):
          return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
     endif;

     return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
}

//
function NumerosCPF($cpf, $numero) {
     $apenasNumeros = preg_replace('/[^0-9]/', '', $cpf); // Remove caracteres não numéricos
     return substr($apenasNumeros, 0, $numero); // Obtém os primeiros seis números
 }

function limparCpfCnpj($entrada)
{
     // Remove caracteres que não sejam números
     $entradaNumerica = preg_replace("/[^0-9]/", "", $entrada);

     // Verifica se é um CPF (11 dígitos) ou CNPJ (14 dígitos) válido
     if (strlen($entradaNumerica) === 11) {
          // É um CPF
          return $entradaNumerica;
     } elseif (strlen($entradaNumerica) === 14) {
          // É um CNPJ
          return $entradaNumerica;
     } else {
          // Não é um CPF nem um CNPJ válido, retorna a entrada original
          return $entrada;
     }
}
//
function formatPhone($phone)
{
     $formatedPhone = preg_replace('/[^0-9]/', '', $phone);
     $matches = [];
     preg_match('/^([0-9]{2})([0-9]{4,5})([0-9]{4})$/', $formatedPhone, $matches);

     if ($matches):
          return '(' . $matches[1] . ') ' . $matches[2] . '-' . $matches[3];
     endif;

     return $phone; // return number without format
}

//
function moedaPhp($str_num)
{
     $resultado = str_replace('.', ',', $str_num); // remove o ponto
     $resultado = str_replace(',', '', $resultado); // substitui a vírgula por ponto
     $resultado = substr_replace($resultado, '.', -2, 0);
     return number_format($resultado, 2, ",", ".");
     //return floatval($resultado); // transforma a saída em FLOAT
}

function formatMoeda($str_num)
{
     $resultado = str_replace('.', '', $str_num); // remove o ponto
     $resultado = str_replace(',', '', $resultado); // substitui a vírgula por ponto
     $resultado = substr_replace($resultado, '.', -2, 0);
     //return number_format($resultado,2,",",".");
     return floatval($resultado); // transforma a saída em FLOAT
}

//
function porcentaje($porcentagem, $valor)
{
     $resultado = str_replace('.', '', $valor); // remove o ponto
     $resultado = str_replace(',', '', $resultado); // substitui a vírgula por ponto
     $resultado = substr_replace($resultado, '.', -2, 0);

     $resultado = ($resultado * $porcentagem / 100);
     return number_format($resultado, 2, ",", ".");
}


//
function extrairNumeros($data)
{
     // Remove caracteres não numéricos
     $valor = preg_replace("/[^0-9]/", "", $data);

     // Verifica se o valor resultante tem 5 ou mais dígitos
     if (strlen($valor) >= 5) {
          return $valor; // retorna os números
     } else {
          return $data; // retorna o valor original
     }
}

//
function calcularTempoTrabalhado($horaEntrada, $horaSaidaAlmoco, $horaRetornoAlmoco, $horaSaida)
{
     date_default_timezone_set('America/Sao_Paulo');

     if ($horaEntrada == null || $horaSaidaAlmoco == null || $horaRetornoAlmoco == null || $horaSaida == null)
          return false;

     // Converter as horas para timestamps
     $entradaTimestamp = strtotime($horaEntrada);

     // Verificar se a saída para o almoço é negativa
     $saidaAlmocoNegativa = false;
     if (strpos($horaSaidaAlmoco, '-') === 0) {
          $horaSaidaAlmoco = substr($horaSaidaAlmoco, 1); // Remover o sinal negativo
          $saidaAlmocoNegativa = true;
     }
     $saidaAlmocoTimestamp = strtotime($horaSaidaAlmoco);

     // Verificar se o retorno do almoço é negativo
     $retornoAlmocoNegativo = false;
     if (strpos($horaRetornoAlmoco, '-') === 0) {
          $horaRetornoAlmoco = substr($horaRetornoAlmoco, 1); // Remover o sinal negativo
          $retornoAlmocoNegativo = true;
     }
     $retornoAlmocoTimestamp = strtotime($horaRetornoAlmoco);

     $saidaTimestamp = strtotime($horaSaida);

     // Calcular o tempo total de trabalho excluindo o período do almoço
     $tempoTrabalhado = ($saidaAlmocoTimestamp - $entradaTimestamp) + ($saidaTimestamp - $retornoAlmocoTimestamp);

     // Se as saídas para o almoço ou retorno do almoço forem negativas, inverter o sinal do tempo total
     if ($saidaAlmocoNegativa || $retornoAlmocoNegativo) {
          $tempoTrabalhado = -$tempoTrabalhado;
     }

     // Converter o tempo total em horas e minutos
     $horas = floor(abs($tempoTrabalhado) / 3600);
     $minutos = floor((abs($tempoTrabalhado) % 3600) / 60);

     // Formatar o resultado
     return sprintf("%02d:%02d", $horas, $minutos);
}

// ------------------------------------------------------------------------------------ //  

//Function conta dias entre datas   
function diasDatas($data_inicial, $data_final)
{
     $dataInicil = date('Y-m-d', strtotime($data_inicial));
     $dataFinal = date('Y-m-d', strtotime($data_final));

     $diferenca = strtotime($dataFinal) - strtotime($dataInicil);
     $dias = floor($diferenca / (60 * 60 * 24));
     return $dias;
}

//
function formatodata($data)
{
     $numero_dia = date('w', strtotime($data)) * 1;
     $dia_mes = date('d', strtotime($data));
     $numero_mes = date('m', strtotime($data)) * 1;
     $ano = date('Y');
     $dia = array('Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado');
     $mes = array('', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
     return $dia[$numero_dia] . ", " . $dia_mes . " de " . $mes[$numero_mes] . " de " . $ano . ".";
}

//
function data_diautiol($data)
{
     $diasemana_numero = date('w', strtotime($data));

     $data_valid = $data;

     if ($diasemana_numero == 0):
          $data_valid = date('Y-m-d', strtotime("+1 days", strtotime($data)));
     endif;

     if ($diasemana_numero == 6):
          $data_valid = date('Y-m-d', strtotime("+2 days", strtotime($data)));
     endif;

     return $data_valid;
}

//
function data_valida($data)
{
     $diasemana_numero = date('w', strtotime($data));

     $data_valid = $data;

     if ($diasemana_numero == 0):
          $data_valid = date('Y-m-d', strtotime("+1 days", strtotime($data)));
     endif;

     if ($diasemana_numero == 6):
          $data_valid = date('Y-m-d', strtotime("+2 days", strtotime($data)));
     endif;

     return $data_valid;
}

//
function fomat_data($data)
{
     $datetime = new DateTime($data);
     $datetime->format('Y-m-d H:i:s') . "\n";
     $la_time = new DateTimeZone('America/Fortaleza');
     $datetime->setTimezone($la_time);

     return $datetime->format('d/m/Y');
}
// 
function data_expira($dt_expira)
{
     $dt_atual = date("Y-m-d");

     $timestamp_dt_atual = strtotime($dt_atual);

     $timestamp_dt_expira = strtotime($dt_expira);

     if ($timestamp_dt_atual > $timestamp_dt_expira):
          $response = 1;
     else: // false
          $response = 0;
     endif;

     return $response;
}
//
function dtexecult($data, $numero)
{
     $data = date('Y-m-d', strtotime("$numero days", strtotime($data)));

     $diasemana_numero = date('w', strtotime($data));

     $data_valid = $data;

     if ($diasemana_numero == 0):
          $data_valid = date('Y-m-d', strtotime("+1 days", strtotime($data)));
     endif;

     if ($diasemana_numero == 6):
          $data_valid = date('Y-m-d', strtotime("+2 days", strtotime($data)));
     endif;

     return $data_valid;
}

//
function criarArrayDeDatas($dataInicio, $dataFim)
{
     $datas = array();
     $dataAtual = new DateTime($dataInicio);

     while ($dataAtual <= new DateTime($dataFim)) {
          $datas[] = $dataAtual->format('Y-m-d');
          $dataAtual->add(new DateInterval('P1D')); // Adiciona 1 dia à data atual
     }

     return $datas;
}

//
function separarNome($nomeCompleto) {
     $partesNome = explode(' ', $nomeCompleto, 2); // Divide o nome em um array usando o espaço como separador
     $primeiroNome = $partesNome[0]; // Primeiro elemento do array é o primeiro nome
     $sobrenome = isset($partesNome[1]) ? $partesNome[1] : ''; // Segundo elemento é o sobrenome (se existir)
 
     return $partesNome; //array($primeiroNome, $sobrenome); // Retorna um array com o primeiro nome e o sobrenome
 }
 