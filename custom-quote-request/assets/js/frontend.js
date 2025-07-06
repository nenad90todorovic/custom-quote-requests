jQuery(document).ready(function ($) {
  
  // add products for quote page
  $('button[data-quote-item]').on('click', function(e) {
    const self = $(this)

    e.preventDefault()

    $.ajax({
      url  : ajaxify.url,
      type : 'post',
      data : {
        nonce : ajaxify.nonce,
        id    : $(this).attr('data-quote-item'),
        action: 'add_product_to_quote'
      },

      // products added
      success: function(results) {
        self.parents('.quote-btn-wrap').addClass('added-product')
        self.text('Added to quote')

        // add results to html if any
        if (results != 0) {
          $('.quote-link').html(results)
        }

      }
    })      
  })  

  // submit quote data into the db
  $('.quote-form').on('submit', function(e) {
    e.preventDefault()

    const self = $(this),
          data = self.serialize()

    $.ajax({
      url  : ajaxify.url,
      beforeSend: function() {
        self.parents('.quote-details').addClass('quote-form-sending')
      },
      type : 'post',
      data : {
        nonce   : ajaxify.nonce,
        formData: data,
        action  : 'submit_quote_data'
      },
      
      // quote data submitted
      success: function(data) {
        const results = JSON.parse(data)

        // success
        if (results.success) {
          setTimeout(() => {
            $('.quote-details > h2').fadeOut(300, function() {
              $(this).remove()
            })

            $('.quote-details > form').fadeOut(300, function() {
              $(this).remove()
              $('.quote-details').append(`<p>${results.msg}</p>`)

              self.parents('.quote-details').removeClass('quote-form-sending')
            })
          }, 500)
        }

        // fail
        else {
          setTimeout(() => {
            alert(results.msg)
            self.parents('.quote-details').removeClass('quote-form-sending')
          }, 500)
        }
      }
    })  
  })

  // remove the product on quote review page
  $('.quote-product-close > *').on('click', function(e) {
    e.preventDefault()
    
    const self           = $(this),
          productItem    = self.parents('.quote-details-item'),
          productId      = (productItem.find('textarea').attr('id')).replace('quote_notes_', ''),
          confirmRemoval = confirm( 'Are you sure?' )

    if (!confirmRemoval) {
      return
    }

    $.ajax({
      url  : ajaxify.url,
      type : 'post',
      data : {
        nonce    : ajaxify.nonce,
        productId: productId,
        action   : 'remove_product'
      },

      // product removed - also redirect if no products available
      success: function(results) {
        if (results == 'success') {
          productItem.fadeOut(300, function() {
            productItem.remove()

            if (!$('.quote-details-item').length) {
              window.location.replace( ajaxify.home + '/shop' )
            }            
          })
        }

        // removal failed
        else {
          alert( 'Failed to remove the product!' )
        }
      }
    })
  })

})