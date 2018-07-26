function (data, res) {
        
        var json2csv = require('json2csv');
        var i=0, 
            loopCnt = 0, 
            records = [], 
            fields = ['S.No', 'Full Name', 'Email', 'Mobile', 'location', 'Test Paper', 'Marks Secured', 'Marks Total'];

        if(data != '' && data != 'undefined' && data.length > 0) {
            for(i; i < data.length; i++) {

                var temp = {"S.No"                   : i+1, 
                            "Full Name"              : data[i].student_name,
                            "Email"                  : data[i].email,
                            "Mobile"                 : data[i].phone,
                            "location"               : data[i].country,
                            "Test Paper"             : data[i].test_paper_name,
                            "Marks Secured"          : data[i].secured_marks,
                            "Marks Total"            : data[i].total_marks};

                var params = {token : data[i].active_token, forExport:temp, loopCnt: i};
                examModel.getStudentWiseReport(params, function(result){
                    
                    if(result != '' && result != 'undefined' && result.length > 0) {

                        var forExport = result.forExport;
                        var loopCnt   = result.loopCnt;

                        var j=0, loopCnt2 = 0;

                        for(j; j < (result.length); j++) {

                            if(fields.indexOf(result[j].section_name+' (Secured)') < 0 && fields.indexOf(result[j].section_name+' (Total)') < 0) {
                                fields.push(result[j].section_name +' (Secured)');
                                fields.push(result[j].section_name +' (Total)');
                            }

                            forExport[result[j].section_name +' (Secured)'] = result[j].marks;
                            forExport[result[j].section_name +' (Total)']   = result[j].total_marks;

                            if( ++loopCnt2 == (result.length) ) {
                                records[loopCnt] = forExport;
                                if( loopCnt == (data.length - 1) ) {
                                    var csvData = json2csv({ data: records, fields: fields });
                                    var fileName = getDateTime.getDateTime('Y-M-D_H-M-I');
                                    res.attachment(fileName+'.csv');
                                    res.status(200).send(csvData);
                                }
                            }
                        }
                    }
                });
            }
        }
    }
