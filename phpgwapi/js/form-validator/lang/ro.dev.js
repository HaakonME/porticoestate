/**
 * jQuery Form Validator
 * ------------------------------------------
 *
 * Romanian language package
 *
 * @website http://formvalidator.net/
 * @license MIT
 * @version 2.2.83
 */
(function($, window) {

  'use strict';

  $(window).bind('validatorsLoaded', function() {

    $.formUtils.LANG = {
      errorTitle: 'Nu sa reusit lansarea formularului!',
      requiredField: 'Acest câmp este obligatoriu',
      requiredfields: 'Nu toate câmpurile obligatorii au fost completate',
      badTime: 'Timpul introdus este incorect',
      badEmail: 'Adresa de e-mail este incorectă',
      badTelephone: 'Numărul de telefon este incorect',
      badSecurityAnswer: 'Răspuns incorect la întrebarea de siguran?ă',
      badDate: 'Dară incorectă',
      lengthBadStart: 'Valoarea introdusă trebuie să fie interval ',
      lengthBadEnd: ' caractere',
      lengthTooLongStart: 'Valoarea introdusă este mai mare decât ',
      lengthTooShortStart: 'Valoarea introdusă este mai mică decât ',
      notConfirmed: 'Valorile introduse nu au fost confirmate',
      badDomain: 'Domeniul este incorect',
      badUrl: 'Adresa URL este incorectă',
      badCustomVal: 'Valoarea introdusă este incorectă',
      andSpaces: ' şi spaţierea',
      badInt: 'Numărul introdus este incorect',
      badSecurityNumber: 'Numărul de asigurare introdus este incorect',
      badUKVatAnswer: 'Numărul CIF introdus este incorect',
      badStrength: 'Parola Dvs nu este suficient de sigură',
      badNumberOfSelectedOptionsStart: 'Trebuie să alegi măcar ',
      badNumberOfSelectedOptionsEnd: ' răspunsuri',
      badAlphaNumeric: 'Valoarea introdusă trebuie să con însă doar caractere alfanumerice ',
      badAlphaNumericExtra: ' și ',
      wrongFileSize: 'Fisierul trimis este prea mare (max %s)',
      wrongFileType: 'Se acceptă doar fisiere tip %s',
      groupCheckedRangeStart: 'Te rog alege între ',
      groupCheckedTooFewStart: 'Te rog alege măcar ',
      groupCheckedTooManyStart: 'Te rog alege maxim ',
      groupCheckedEnd: ' elemnt(e)',
      badCreditCard: 'Numărul de card introdus este incorect',
      badCVV: 'Numărul CVV introdus este incorect',
      wrongFileDim: 'Dimensiunea imaginii este incorectă,',
      imageTooTall: 'imaginea nu poate fi mai înaltă decât',
      imageTooWide: 'imaginea nu poate fi mai lată decât',
      imageTooSmall: 'imaginea este prea mică',
      min: 'min',
      max: 'max',
      imageRatioNotAccepted: 'Proportiile imaginii sunt incorecte',
      badBrazilTelephoneAnswer: 'Numărul de telefon introdus este incorect.',
      badBrazilCEPAnswer: 'CEP incorect',
      badBrazilCPFAnswer: 'CPF incorect'
    };

  });

})(jQuery, window);
