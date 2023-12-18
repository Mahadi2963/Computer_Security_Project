<?php 
include 'admin/db_connect.php'; 
?>
<style>

    
#portfolio .img-fluid{
    width: calc(100%);
    height: 30vh;
    z-index: -1;
    position: relative;
    padding: 1em;
}
.alumni-list{
cursor: pointer;
border: unset;
flex-direction: inherit;
}
.alumni-img {
    width: calc(50%);
    max-height: 30vh;
    display: flex;
    align-items: center;
    justify-content: center;
}
.alumni-list .card-body{
    width: calc(70%);
}
.alumni-img img {
    border-radius: 100%;
    max-height: calc(100%);
    max-width: calc(100%);
}






</style>

<br><br><br><br>

<h3 style="text-align: center;" class="text-dark">Participants List</h3>
        	<div class="container">
        		<div class="card mb-2 mt-4">

        

		        <div class="card-body ">
		            <div class="row">
		                <div class="col-md-8">
		                    <div class="input-group mb-3">
		                      <div class="input-group-prepend">
		                        <span class="input-group-text" id="filter-field"><i class="fa fa-search"></i></span>
		                      </div>
		                      <input type="text" class="form-control" id="filter" placeholder="Filter name,course, etc." aria-label="Filter" aria-describedby="filter-field">
		                    </div>
		                </div>
		                <div class="col-md-4">
		                    <button class="btn btn-primary btn-block btn-sm" id="search">Search</button>
		                </div>
		            </div>
		            
		        </div>

                
		    </div>
        	</div>	



            
            <div class="container-fluid mt-3 pt-2">
               
                <div class="row-items">
                <div class="col-lg-12">
                    <div class="row">
                <?php
                $fpath = 'admin/assets/uploads';
                $alumni = $conn->query("SELECT a.*,c.course,Concat(a.firstname,' ',a.lastname) as name from alumnus_bio a inner join courses c on c.id = a.course_id order by Concat(a.lastname,', ',a.firstname,' ',a.middlename) asc");
                while($row = $alumni->fetch_assoc()):
                ?>
                <div class="container">
                    <!-- designs here -->
                <div class="col-md-12 item"> 
                    <!-- designs end -->
                <div class="card alumni-list" data-id="<?php echo $row['id'] ?>">
                        <div class="alumni-img" card-img-top>

                            <img src="<?php echo $fpath.'/'.$row['avatar'] ?>" alt="">
                        </div>
                    <div class="card-body">
                        <div class="row align-items-center h-100">
                            <div class="">
                                <div>
                                <p class="filter-txt"><b><?php echo $row['name'] ?></b></p>
                                <hr class="divider w-100" style="max-width: calc(100%)">
                                <p class="filter-txt">Email: <b><?php echo $row['email'] ?></b></p>
                                <p class="filter-txt">Department: <b><?php echo $row['course'] ?></b></p>
                                <p class="filter-txt">Batch: <b><?php echo $row['batch'] ?></b></p>
                                <p class="filter-txt">Currently working in/as <b><?php echo $row['connected_to'] ?></b></p>
                                    <br>
                                </div>
                            </div>
                        </div>
                        

                    </div>
                </div>
                </div>
                <br>
                </div>
                <?php endwhile; ?>
                </div>
                </div>
                </div>
            </div>


<script>
   
    $('.book-alumni').click(function(){
        uni_modal("Submit Booking Request","booking.php?alumni_id="+$(this).attr('data-id'))
    })
    $('.alumni-img img').click(function(){
        viewer_modal($(this).attr('src'))
    })
     $('#filter').keypress(function(e){
    if(e.which == 13)
        $('#search').trigger('click')
   })
    $('#search').click(function(){
        var txt = $('#filter').val()
        start_load()
        if(txt == ''){
        $('.item').show()
        end_load()
        return false;
        }
        $('.item').each(function(){
            var content = "";
            $(this).find(".filter-txt").each(function(){
                content += ' '+$(this).text()
            })
            if((content.toLowerCase()).includes(txt.toLowerCase()) == true){
                $(this).toggle(true)
            }else{
                $(this).toggle(false)
            }
        })
        end_load()
    })

</script>