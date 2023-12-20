//
function chekform(form) {
    var cont = 0;

    var inputs = document.getElementById(form).getElementsByTagName('input');

    for (var i = 0; i < inputs.length; i++) {
        if (inputs[i].value == '') {
            cont++;
            inputs[i].style.background = '#ff000080';
        }
    }

    var select = document.getElementById(form).getElementsByTagName('select');

    for (var i = 0; i < select.length; i++) {
        if (select[i].value == '') {
            cont++;
            select[i].style.background = '#ff000080';
        }
    }

    return cont;
}

//
function FromData(form) {
    var formValues = {};

    var inputs = document.getElementById(form).getElementsByTagName('input');

    for (var i = 0; i < inputs.length; i++) {
        formValues[inputs[i].name] = inputs[i].value;
    }

    var select = document.getElementById(form).getElementsByTagName('select');

    for (var i = 0; i < select.length; i++) {
        formValues[select[i].name] = select[i].value;
    }

    var textarea = document.getElementById(form).getElementsByTagName('textarea');

    for (var i = 0; i < textarea.length; i++) {
        formValues[textarea[i].name] = textarea[i].value;;
    }

    return formValues;
}

//
function textareaEdit(form, textarea) {
    return document.getElementById(form).querySelector("iframe").contentDocument.body.innerHTML; //getElementById('tinymce')[textarea].innerHTML;    
}

//
function form_juridico() {
    let cadastro = document.getElementById('cadastro').value;

    switch (cadastro) {
        case 'juridico':
            axios.get('/cliente/cardjuridico')
            .then(function (res) {
                document.getElementById('form-juridico').innerHTML = res.data;
             });
            break;

        default:
            document.getElementById('form-juridico').innerHTML = '';
            break;
    }
}

//
function validarJSON(jsonString) {
    try {
        return JSON.parse(jsonString);
    } catch (error) {
        return jsonString;
    }
}

//
function printJsonds(form, jsonds) {
    if (jsonds != null) {
        const obj = validarJSON(jsonds);

        var inputs = document.getElementById(form).getElementsByTagName('input');

        for (var i = 0; i < inputs.length; i++) {
            document.getElementById(inputs[i].name).value = obj[inputs[i].name];
        }

        var textaria = document.getElementById(form).getElementsByTagName('textarea');

        for (var i = 0; i < textaria.length; i++) {
            document.getElementById(form).getElementsByClassName('tox-edit-area__iframe')[i].contentDocument.body.innerHTML = obj[textaria[i].name];
        }

        var select = document.getElementById(form).getElementsByTagName('select');

        for (var i = 0; i < select.length; i++) {
            document.getElementById(select[i].name).innerHTML = obj[select[i].name];
        }
    }
    else {
        var inputs = document.getElementById(form).getElementsByTagName('input');

        for (var i = 0; i < inputs.length; i++) {
            document.getElementById(inputs[i].name).value = '';
        }

        var textaria = document.getElementById(form).getElementsByTagName('textarea');

        for (var i = 0; i < textaria.length; i++) {
            document.getElementById(form).getElementsByClassName('tox-edit-area__iframe')[i].contentDocument.body.innerHTML = '';
        }
    }
    // console.log(dados);
}

//Buscar Dados pelo CEP
function buscarCEP() {
    const url = 'https://ws.apicep.com/cep.json?code=';
    let postalCode = document.getElementById('postalCode').value;

    axios.get(url + postalCode).then(function (res) {
        console.log(res.data);

        let Obj = res.data;

        if (Obj.status == 400)
            return swal(Obj.message, {
                icon: "warning",
            });

        document.getElementById("address").value = Obj.address;
        document.getElementById("province").value = Obj.district;
        document.getElementById("cityandstate").value = Obj.city + ' ' + Obj.state;
    });
}

//
function pesquisa() {
    const pesquisa = document.getElementById('pesquisa').value;

    let request_uri = location.pathname + location.search;

    // Verifique se a URL contém '?pesquisa='
    if (request_uri.indexOf('?pesquisa=') !== -1 || request_uri.indexOf('&pesquisa=') !== -1) {
        // Substitua o valor existente
        request_uri = request_uri.replace(/(\?|\&)pesquisa=([^&]*)/, '$1pesquisa=' + pesquisa);
    } else {
        // Adicione '?pesquisa='
        if (request_uri.indexOf('?') !== -1) {
            request_uri += '&pesquisa=' + pesquisa;
        } else {
            request_uri += '?pesquisa=' + pesquisa;
        }
    }

    window.location.href = request_uri;
}

//
function pesquisaPerido(){
    const inicil = document.getElementById('inicil').value;
    const fim = document.getElementById('fim').value;

    let request_uri = location.pathname + location.search;

    if (request_uri.indexOf('?inicil=') !== -1) {
        // Substitua o valor existente
        request_uri = request_uri.replace(/(\?|\&)inicil=([^&]*)/, '$1inicil=' + inicil)+'&fim='+fim;
    } else {
        request_uri += '?inicil='+inicil+'&fim'+fim;
    }
    window.location.href = request_uri;
}

//
function homePesquisa() {
    let request_uri = location.pathname + location.search;
    request_uri = request_uri.replace(/(\?|\&)pesquisa=([^&]*)/, '');
    window.location.href = location.pathname;
}

//
function formatMoeda(str_num) {
    let resultado = str_num.replace(/\./g, ''); // remove os pontos
    resultado = resultado.replace(',', '.'); // substitui a vírgula por ponto
    resultado = resultado.slice(0, -2) + '.' + resultado.slice(-2);
    return parseFloat(resultado); // transforma a saída em FLOAT
}

//
function moeda(a, e, r, t) {
    //
    let n = "",
        h = j = 0,
        u = tamanho2 = 0,
        l = ajd2 = "",
        o = window.Event ? t.which : t.keyCode;

    if (13 == o || 8 == o)
        return !0;

    if (n = String.fromCharCode(o),
        -1 == "0123456789".indexOf(n))
        return !1;

    for (u = a.value.length,
        h = 0; h < u && ("0" == a.value.charAt(h) || a.value.charAt(h) == r); h++)
        ;

    for (l = ""; h < u; h++)
        -
            1 != "0123456789".indexOf(a.value.charAt(h)) && (l += a.value.charAt(h));

    if (l += n,
        0 == (u = l.length) && (a.value = ""),
        1 == u && (a.value = "0" + r + "0" + l),
        2 == u && (a.value = "0" + r + l),
        u > 2) {

        for (ajd2 = "",
            j = 0,
            h = u - 3; h >= 0; h--)
            3 == j && (ajd2 += e,
                j = 0),
                ajd2 += l.charAt(h),
                j++;
        for (a.value = "",
            tamanho2 = ajd2.length,
            h = tamanho2 - 1; h >= 0; h--)
            a.value += ajd2.charAt(h);
        a.value += r + l.substr(u - 2, u)
    }
    return !1
} 

//
function select_checkbox(input, valor)
{
     const data = document.getElementById(input).value = valor;
}

//
function printDiv(data){	
    var mywindow = window.open('', 'PRINT', '');
    var link = document.getElementsByTagName("link");
   
    let head = '';
   
    // for( var i = 0; i < link.length; i++ ){
    //     head += '<link rel="stylesheet" href="'+link[i].href+'">';
    //    }

    head += '<link rel="stylesheet" href="https://gestor.locaveiculos.com.br/resources/css/form.styles.css">';

    mywindow.document.write('<html><head>');
    mywindow.document.write(head);
    mywindow.document.write('</head><body class="print-body"><div class="print-conteiner">');
    mywindow.document.write(document.getElementById(data).innerHTML);
    mywindow.document.write('</div></body></html>');

    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10*/

    setTimeout(function () {
        mywindow.print();
        mywindow.close();
       }, 1000)
    return true;
}
