$(function () {
    //选择时间的文本框中加class="timeSelectSingle";.live('change', function(e) {});
    $('.timeSelectSingle').live('click', function(e) {
        $('#timeSelectSingle_div').remove();

        var hourOpts = '';
        for (i = 0; i <= 24; i++) if(i<10){ hourOpts += '<option>' +0+i + '</option>'}else{ hourOpts += '<option>' + i + '</option>'};
        var minuteOpts = '<option>00</option><option>15</option><option>30</option><option>45</option>';

        var html = $('<div id="timeSelectSingle_div"><select id="timeSelectSingle_a">' + hourOpts +
			'</select>:<select id="timeSelectSingle_b">' + minuteOpts +
			'</select>&nbsp;<input type="button" value="确定" id="timeSelectSingle_btn" />&nbsp;<input type="button" value="清除" id="timeSelectSingle_btnclear" /></div>')
			.css({
			    "position": "absolute",
			    "z-index": "999",
			    "padding": "5px",
			    "border": "1px solid #AAA",
			    "background-color": "#FFF",
			    "box-shadow": "1px 1px 3px rgba(0,0,0,.4)"
			})
			.css({ "left": $(this).offset().left - $("#myModal").offset().left})
			.css({ "top": $(this).offset().top + $(this).get(0).offsetHeight - $("#myModal").offset().top })
			.click(function () { return false });

        // 如果文本框有值
        var v = $(this).val();
        if (v) {
            v = v.split(/:|-/);
            html.find('#timeSelectSingle_a').val(v[0]);
            html.find('#timeSelectSingle_b').val(v[1]);
        }
        // 点击确定的时候
        var pObj = $(this);
        html.find('#timeSelectSingle_btn').click(function () {
            var str = html.find('#timeSelectSingle_a').val() + ':'
				+ html.find('#timeSelectSingle_b').val();
            pObj.val(str);
            $('#timeSelectSingle_div').remove();
        });
        html.find('#timeSelectSingle_btnclear').click(function () {
            var str = "";
            pObj.val(str);
            $('#timeSelectSingle_div').remove();
        });
        
//       添加针对timeSelectSingle_b的hour控件 在24点时分钟只为00
		html.find('#timeSelectSingle_a').change(function () {
        	if($('#timeSelectSingle_a').val() == 24){
        		$('#timeSelectSingle_b').empty();
        		$('#timeSelectSingle_b').prepend('<option>00</option>');
        	}else{
        		$('#timeSelectSingle_b').empty();
        		$('#timeSelectSingle_b').prepend(minuteOpts);
        		
        	}
       });

		$(this).after(html);
		
		 if($('#timeSelectSingle_a').val() == 24){
        		$('#timeSelectSingle_b').empty();
        		$('#timeSelectSingle_b').prepend('<option>00</option>');
        	}else{
        		$('#timeSelectSingle_b').empty();
        		$('#timeSelectSingle_b').prepend(minuteOpts);
        		
        	}
        	
        return false;
    });
    
   
        	
    $('.timeSelectSingle2').live('click', function(e) {
        $('#timeSelectSingle_div').remove();

        var hourOpts = '';
        for (i = 0; i < 24; i++) if(i<10){ hourOpts += '<option>' +0+i + '</option>'}else{ hourOpts += '<option>' + i + '</option>'};
        var minuteOpts = '<option>00</option><option>15</option><option>30</option><option>45</option>';

        var html = $('<div id="timeSelectSingle_div"><select id="timeSelectSingle_a">' + hourOpts +
			'</select>:<select id="timeSelectSingle_b">' + minuteOpts +
			'</select>&nbsp;<input type="button" value="确定" id="timeSelectSingle_btn" />&nbsp;<input type="button" value="清除" id="timeSelectSingle_btnclear" /></div>')
			.css({
			    "position": "absolute",
			    "z-index": "999",
			    "padding": "5px",
			    "border": "1px solid #AAA",
			    "background-color": "#FFF",
			    "box-shadow": "1px 1px 3px rgba(0,0,0,.4)"
			})
			.css({ "left": $(this).offset().left - $("#myModal").offset().left})
			.css({ "top": $(this).offset().top + $(this).get(0).offsetHeight - $("#myModal").offset().top })
			.click(function () { return false });

        // 如果文本框有值
        var v = $(this).val();
        if (v) {
            v = v.split(/:|-/);
            html.find('#timeSelectSingle_a').val(v[0]);
            html.find('#timeSelectSingle_b').val(v[1]);
        }
        // 点击确定的时候
        var pObj = $(this);
        html.find('#timeSelectSingle_btn').click(function () {
            var str = html.find('#timeSelectSingle_a').val() + ':'
				+ html.find('#timeSelectSingle_b').val();
            pObj.val(str);
            $('#timeSelectSingle_div').remove();
        });
        html.find('#timeSelectSingle_btnclear').click(function () {
            var str = "";
            pObj.val(str);
            $('#timeSelectSingle_div').remove();
        });
        $(this).after(html);
        return false;
    });
    //
    $(document).click(function () {
        $('#timeSelectSingle_div').remove();
    });
    //
});
