((window, $) => {
  $(() => {
    const dirname = (() => { // {{{
      let ret = '/';
      $('script').each(function() {
        const src = String($(this).attr('src'))
          .replace(/#[^#]+$/, '') // フラグメントの削除
          .replace(/\?[^?]+$/, ''); // クエリパラメータの削除
        const match = src.match(/^(^|.*\/)tz-init\.js$/);
        if (match) {
          ret = match[1].replace(/\/$/, ''); // 末尾の / の削除
          return false;
        }
      });
      return ret;
    })(); // }}}
    
    timezoneJS.timezone.zoneFileBasePath = `${dirname}/files`;
    timezoneJS.timezone.init();
  });
})(window, jQuery);
