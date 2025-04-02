@if($AccontoRichiesta != 0 || $AccontoLibero != 0 || $AccontoPercentuale != 0 || $AccontoImporto != 0)
    <div class="boxquoto" id="pagamento">
        <div class="box6">
            <h3><?=strtoupper(dizionario('ACCONTO_OFFERTA'))?></h3>
        </div>
        <?php 
            $box     ='pagamento'; //ID del box contenitore
            $frase1  = dizionario('VISUALIZZA').' '.dizionario('ACCONTO_OFFERTA');
            $frase2  = dizionario('NASCONDI').' '.dizionario('ACCONTO_OFFERTA');
            $bollino ='<i class="fal fa-check"></i>'; //font awesome di riferimento
            $oc      ="1";//1 aperto - 0 chiuso
        ?>
        @include('/smart/include/inc_OC.php'); 

            <div class="box6 t14 content">

                <div class="m m-x-12 m-m-12 m-s-12 m-s-ha">
                    <div class="box6">

                            <h2><?=ucfirst($Nome)?> <?=ucfirst($Cognome)?> <span class="t18"><i class="fas fa-long-arrow-alt-right fas-sx"></i> <?=dizionario('OFFERTA')?> nr. <?=$NumeroPrenotazione?> <?=dizionario('DEL')?> <?=$DataRichiesta?></span></h2>
                            <?if($AccontoRichiesta != 0 && $AccontoLibero != 0 || $AccontoPercentuale != 0 || $AccontoImporto != 0) {?>
                                <h3><?=dizionario('SCADENZA_OFFERTA')?> <span class="tcolor"><?=$DataScadenza?></span></h3>
                            <?}else{?>
                                <h3><?=dizionario('SCADENZA')?> <?=dizionario('OFFERTA')?> <span class="tcolor"><?=$DataScadenza?></span></h3>
                            <?}?>
                            
                            @if($ordinamento_pagamenti)
                                @foreach ($ordinamento_pagamenti as $chiave_pagamenti => $valore_pagamenti)
                                    <?=$valore_pagamenti;?>
                                @endforeach
                            @endif

                    </div>
                    <!-- FORM CARTA -->
                
                    </div>
                </div>
                <div class="ca"></div>
            </div>
            <div class="ca"></div>
    </div>
    <style>
        #preno .titolo {
            font-size: 20px;
            font-weight: 300;
        }

        #preno .fproposta {
            margin-bottom: 1px;
            border-radius: 5px;
            transition: all .8s ease;
            cursor: pointer;
            margin-left: 50px;
            background-color: #999 !important;
            width: calc(100% - 50px) !important;
        }

        #preno .fproposta:hover {
            transition: all .01s ease;
            background-color: #666;
        }

        #preno .fproposta.selected {
            opacity: 1;
            background-color: <?=$colore1?> !important;
        }

        #preno .riga {
            padding: 15px;
        }

        #preno .fproposta .specchietto {
            display: none;
        }

        #preno .specchietto linea {
            background-color: rgba(0, 0, 0, 0.2);
            font-size: 12px;
            font-weight: 700;
            position: relative;
            clear: both;
            display: block;
            margin-bottom: 1px;
            padding: 5px 0px 5px 80px;
            width: 100%;
        }

        #preno .specchietto linea svg {
            position: absolute;
            font-size: 20px;
            left: 25px;
            top: 50%;
            transform: translateY(-50%);
        }

        #preno .specchietto totale {
            display: block;
            font-weight: 300;
            font-size: 18px;
            text-decoration: line-through;
        }

        #preno .specchietto sconto {
            display: block;
            font-weight: 300;
            font-size: 18px;
        }

        #preno .specchietto newtotale {
            display: block;
            font-weight: 700;
            font-size: 28px;
        }

        .fproposta .fa-circle,
        .fproposta .fa-check-circle {

            font-size: 30px;
            position: absolute;
            top: 13px;
            left: -40px;
            color: #999;
        }

        .fproposta .fa-check-circle {
            opacity: 0;
        }

        .fproposta.selected .fa-check-circle {
            opacity: 1;
            color: <?=$colore1?> !important;
        }

        .fproposta.selected .fa-circle {
            opacity: 0;
        }
        .boxform{
            padding: 5px;
            border-radius: 5px;
        }
        .formproposta.choosen .scegli{
            display: none;
        }
        .formproposta.choosen .conferma{
            display: block;

        }
    </style>
@endif