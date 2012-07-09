$(document).ready(function()
{
   // Notice the use of the each() method to acquire access to each elements attributes
   $('#box a[tooltip]').each(function()
   {
      $(this).qtip({
         content: $(this).attr('tooltip'), // Use the tooltip attribute of the element for the content
         style: 'cream' // Give it a crea mstyle to make it stand out
      });
   });
});