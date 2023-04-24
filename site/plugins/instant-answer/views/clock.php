
<div class="card shadow-sm my-2 ia-card">
    <div class="card-body">
        <h3 class="card-title h4 ia-title"><?= $t['ia_title']; ?></h3>
        <div class="ia-body <?= e_attr($t->get('ia_body_class')); ?>">
            <?= $t['ia_body']; ?>
            <div id="ia-clock" class="clock h5"></div>
        </div>
    </div>
</div>


<script type="text/javascript">
    function showTime()
    {
    var date = new Date();
    var h = date.getHours(); // 0 - 23
    var m = date.getMinutes(); // 0 - 59
    var s = date.getSeconds(); // 0 - 59
    var session = "AM";
    
    if(h == 0){
        h = 12;
    }
    
    if(h > 12){
        h = h - 12;
        session = "PM";
    }
    
    h = (h < 10) ? "0" + h : h;
    m = (m < 10) ? "0" + m : m;
    s = (s < 10) ? "0" + s : s;
    
    var time = h + ":" + m + ":" + s + " " + session;
    document.getElementById("ia-clock").innerText = time;
    document.getElementById("ia-clock").textContent = time;
    
    setTimeout(showTime, 1000);
    
}

showTime();
</script>
