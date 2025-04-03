<div class="boxquoto" id="questionario">
    <div class="box6">
        <h3><?=strtoupper(dizionario('QUESTIONARIO'))?></h3>
    </div>
    <?php 

        $box     ='questionario'; //ID del box contenitore
        $frase1  = dizionario('VISUALIZZA').' '.dizionario('QUESTIONARIO');
        $frase2  = dizionario('NASCONDI').' '.dizionario('QUESTIONARIO');
        $bollino ='<i class="fal fa-check"></i>'; //font awesome di riferimento
        $oc      ="1";//1 aperto - 0 chiuso
	?>
    @include('smart_template/include/inc_OC') 
        <div class="box6 t14 content">
            <div class="m m-x-12 m-m-12 m-s-12 m-s-ha">
                <div class="box6">
                      <span class="t25"><?=str_replace("[cliente]",($Nome.' '.$Cognome),dizionario('TESTO_QUESTIONARIO'))?></span>
                      <div class="ca50"></div>
                        <? if($tot_cs > 0){?>
                              <span class="t30"><?=dizionario('NO_QUESTIONARIO')?></span>
                        <?}else{?>
                            <p>
                            LEGENDA:    <img src="https://www.quotocrm.it/img/emoji/bad.png" style="width:20px;height:20px" data-toogle="tooltip" title="Bad [valore = 1]">(1)
                                        <img src="https://www.quotocrm.it/img/emoji/semi_bad.png" style="width:20px;height:20px" data-toogle="tooltip" title="Semi Bad  [valore = 2]">(2)
                                        <img src="https://www.quotocrm.it/img/emoji/medium.png" style="width:20px;height:20px" data-toogle="tooltip" title="Medium  [valore = 3]">(3)
                                        <img src="https://www.quotocrm.it/img/emoji/semi_good.png" style="width:20px;height:20px" data-toogle="tooltip" title="Semi Good  [valore = 4]">(4)
                                        <img src="https://www.quotocrm.it/img/emoji/good.png" style="width:20px;height:20px" data-toogle="tooltip" title="Good  [valore = 5]">(5)

                          </p>
                          <div class="ca50"></div>
                           <form id="form_quest" name="form_quest" method="post" action="/save_questionario" onsubmit="return controlla();">                                           
                             <?=$question?>
                             <div class="ca20"></div>
                                     <input type="hidden" name="email_hotel" value="<?=$EmailCliente?>">
                                    <input type="hidden" name="nome_hotel" value="<?=$NomeCliente?>"> 
                                    <input type="hidden" name="email_utente" value="<?=$Email?>">
                                    <input type="hidden" name="nome_utente" value="<?=$Cliente?>">
                                    <input type="hidden" name="id_richiesta" value="<?=$Id?>">
                                    <input type="hidden" name="idsito" value="<?=$idsito?>"> 
                                    <input type="hidden" name="data_compilazione" value="<?=date('Y-m-d')?>">
                                    <input type="hidden" name="action" value="send_quest">                                                             
                                    <button type="submit" class="pulsante" id="send_msg"><?=dizionario('INVIA_GIUDIZI')?> <i class="fa fa-angle-double-right"></i></button>                      
                          </form> 
                      <?}?>

                  </div>
                </div>
            </div>
            <div class="ca"></div>
        </div>
        <div class="ca"></div>
</div>

