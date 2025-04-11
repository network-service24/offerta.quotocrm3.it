
  function chiudi()
  {             
      $(".CF").hide();               
  }

  function apri()
  {             
      $(".CF").show(); 
      $("#TipoDocumento").attr("required", true);
      $("#NumeroDocumento").attr("required", true);
      $("#ComuneEmissione").attr("required", true);
      $("#DataRilascio").attr("required", true);
      $("#StatoEmissione").attr("required", true);
      $("#DataScadenza").attr("required", true);         
  }

  function ctrl()
  { 
      if($("#Cittadinanza").val() != 'Italia'){                        
          $(".reg").hide(); 
          $(".prov").hide();                 
          $(".cit").hide();
          $(".cp").hide(); 
          $(".cit_bis").show(); 
          $(".prov_bis").show();
          $(".vuoto").show();                                          
      }else{
          $(".reg").show(); 
          $(".prov").show();
          $(".prov_bis").hide();
          $(".cit").show();
          $(".cit_bis").hide();
          $(".cp").show();
          $(".vuoto").hide();                                               
      }
  }

 function ctrl2()
 { 
      if($("#StatoNascita").val() != 'Italia'){               
          $(".reg2").hide(); 
          $(".prov2").hide(); 
          $(".cit2").hide();
          $(".prov2_bis").show();
          $(".cit2_bis").show();
          $(".vuoto2").show();                                             
      }else{
          $(".reg2").show(); 
          $(".prov2").show();
          $(".prov2_bis").hide();
          $(".cit2").show();
          $(".cit2_bis").hide();
          $(".vuoto2").hide();                                               
      }
 }
  $(document).ready(function() 
  {

      $("#foo").on("change",function(){
          var valore = $("#foo").val();
          if(valore==''){
              chiudi();
          }
          if(valore=='Capo Famiglia'){
              apri();
          }
          if(valore=='Familiare'){
              chiudi();
          }
          if(valore=='Capo Gruppo'){
              apri();
          }
          if(valore=='Membro Gruppo'){
              chiudi();
          }
          if(valore=='Ospite Singolo'){
              apri();
          }
      })

      
      $("[data-toggle=tooltip]").tooltip({
          placement: $(this).data("placement") || 'top'
      });

      
      // validate signup form on keyup and submit
      $("#checkin_form").validate({
          rules: {
              'TipoComponente':{
                  required: true,
                  minlength: 1
              },                
              Nome: "required",
              Cognome: "required",
              Sesso: "required",
              Cittadinanza: "required",
              Indirizzo: "required"
              
              
          },
          messages: {
              Nome: "",
              Cognome: "",
              'TipoComponente': "Selezionare almeno una voce"
          }
      });

  }); 