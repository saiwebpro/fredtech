<script type="text/javascript">
  $(function () {
    <?php if ($t['form_countdown'] > 0) : ?>
    var form_content = $('#form-wrap');
    var form_counter = $('#form-counter-wrap');
    form_counter.fadeIn();

    var time = new Date();
    time.setSeconds(time.getSeconds() + <?= $t['form_countdown']; ?>);
    var classes = 'bg-white text-black-50 rounded shadow-lg p-5 m-2 h4';
    $('#form-counter').countdown({
        date: time,
        render: function (data) {
          var el = $(this.el);
          el.empty()
          .append("<div class=' " + classes + " '>" + this.leadingZeros(data.min, 2) + " <span>min</span></div>")
          .append("<div class='" + classes + "'>" + this.leadingZeros(data.sec, 2) + " <span>sec</span></div>");
      },
      onEnd: function () {
        form_counter.fadeOut();
        form_content.fadeIn();
    }
});
    <?php endif;?>
  });
</script>
