jQuery(document).ready(function($) {
   const savedStatus = localStorage.getItem('membershipStatus');
    $('.accountCompleted').hide();
    $('.surveyNotTaken').hide();
    $('.profileNotCompleted').hide();
    
   if( savedStatus == 0){
    $('.accountCompleted').hide();
    $('.surveyNotTaken').hide();
    $('.profileNotCompleted').show();
   }
   else if( savedStatus == 2){
    $('.accountCompleted').hide();
    $('.surveyNotTaken').show();
    $('.profileNotCompleted').hide();
   }
   else if( savedStatus == 4){
    $('.accountCompleted').show();
    $('.surveyNotTaken').hide();
    $('.profileNotCompleted').hide();
    
   }
   else{
    $('.accountCompleted').hide();
    $('.surveyNotTaken').hide();
    $('.profileNotCompleted').hide();
    
   }
    
});
