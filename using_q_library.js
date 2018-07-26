var Q           = require("q");

var examReports = module.exports = {

fetchAllHotesResultsOfAStudent : function (student_id, studentLoopCnt, batch_id, hot_examIds) {
        var deferred = Q.defer();
        examModel.getAllHotesResultsOfAStudent(student_id, studentLoopCnt, hot_examIds, function(marks_list, sLoopCnt, student) {
            try {
                Q.allSettled([
                    examReports.sortHotMarksList(marks_list.SGA),
                    examReports.sortHotMarksList(marks_list.SGEM)
                ]).spread(function(SGA, SGEM){
                    
                    SGA  = SGA.value;
                    SGEM = SGEM.value;
                    SGA_length = Object.keys(SGA).length;
                    
                    var percentage_sum = 0, k=0;
                    
                    if(SGA_length > 0) {
                        for(var j in SGA) {
                            var marks = 0;
                            if(SGEM[j] != 'undefined') { marks = SGEM[j].marks; } else { marks = SGA[j].marks; }
                            percentage_sum += ( marks / SGA[j].marks ) * 100;
                            
                            if(k == (SGA_length - 1) ) {
                                deferred.resolve({studentLoopCnt:sLoopCnt, student_id:student_id, hotPercent:(percentage_sum / hot_examIds.length).toFixed(2)});
                            }
                            k++;
                        } 
                    } else {
                        deferred.resolve({studentLoopCnt:sLoopCnt, student_id:student_id, hotPercent:0});
                    }
                });
            } catch(err) {
                errLogger.log_error(err.message);
                deferred.reject({'Msg':'FAIL', 'error': 'true', 'errorMsg': 'Something went wrong.'});
            }
        });
        return deferred.promise;
    }
    }
