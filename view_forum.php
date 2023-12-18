<?php 
include 'admin/db_connect.php'; 
 $topic = $conn->query("SELECT *,u.name from forum_topics f inner join users u on u.id = f.user_id  where f.id = ".$_GET['id']);
 foreach($topic->fetch_array() as $k=>$v){
 	if(!is_numeric($k))
 		$$k = $v;
 }
?>
<style>
    h3{
        text-align: center;
    }
</style>

<br><br><br><br>

<h3 class="text-white"><?php echo $title ?></h3>



    <?php echo $title ?>
                
    <?php echo $name ?>
   



<div class="row col-md-12 mb-2 justify-content-center">
                   <span class="badge badge-primary px-3 pt-1 pb-1">
                        <b><i>Topic Created by: <?php echo $name ?></i></b>
                    </span>
            </div>   


<div class="container mt-3 pt-2">
    <div class="card mb-4">
        <div class="card-body">
	            <?php echo html_entity_decode($description) ?>
        <hr class="divider">
        </div>
    </div>
  	<?php 
  	
  	$comments = $conn->query("SELECT f.*,u.name,u.username FROM forum_comments f inner join users u on u.id = f.user_id where f.topic_id = $id order by f.id asc");
  	?>
    <div class="card mb-4">
    	<div class="card-body">
    		<div class="col-lg-12">
    			<div class="row">
    				<h3><b> <i class="fa fa-comments"></i> <?php echo $comments->num_rows ?> Comments</b></h3>
    			</div>
    			<hr class="divider" style="max-width: 100%">
    			<?php 
    			while($row= $comments->fetch_assoc()):
    			?>
    			<div class="form-group comment">
                    <?php if($_SESSION['login_id'] == $row['user_id']): ?>
                    <div class="dropdown float-right">
                      <a class="text-dark" href="javascript:void(0)" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="fa fa-ellipsis-v"></span>
                      </a>
                      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item edit_comment" data-id="<?php echo $row['id'] ?>" href="javascript:void(0)">Edit</a>
                        <a class="dropdown-item delete_comment" data-id="<?php echo $row['id'] ?>" href="javascript:void(0)">Delete</a>
                      </div>
                    </div>
                    <?php endif; ?>
    				<p class="mb-0"><large><b><?php echo $row['name'] ?></b></large></p>
    				<small class="mb-0"><i><?php echo $row['username'] ?></i></small>
    				<br>
    				<?php echo html_entity_decode($row['comment']) ?>
    				<hr>
    			</div>
    		<?php endwhile; ?>
    		</div>
    			<hr class="divider" style="max-width: 100%">
    			<div class="col-lg-12">
    				<form action="" id="manage-comment">
    					<div class="form-group">
    						<input type="hidden" name="topic_id" value="<?php echo isset($id) ? $id : '' ?>">
    						<textarea class="form-control jqte" name="comment" cols="30" rows="5" placeholder="New Comment"></textarea>
    					</div>
    					<button class="btn btn-primary">Save Comment</button>
    				</form>
    			</div>
    	</div>
    </div>
    
</div>
    


<script>
  
	$('.jqte').jqte();

    $('#new_forum').click(function(){
        uni_modal("New Topic","manage_forum.php",'mid-large')
    })
    $('.edit_comment').click(function(){
        uni_modal("Edit Comment","manage_comment.php?id="+$(this).attr('data-id'),'mid-large')
    })
    $('.view_topic').click(function(){
        uni_modal("Career Opportunity","view_Forums.php?id="+$(this).attr('data-id'),'mid-large')
    })

    $('#search').click(function(){
        var txt = $(this).val()
        start_load()
        $('.Forum-list').each(function(){
            var content = $(this).text()
            if((content.toLowerCase()).includes(txt.toLowerCase) == true){
                $(this).toggle('true')
            }else{
                $(this).toggle('false')
            }
        })
        end_load()
    })
    $('#manage-comment').submit(function(e){
		e.preventDefault()
		start_load()
		$.ajax({
			url:'admin/ajax.php?action=save_comment',
			method:'POST',
			data:$(this).serialize(),
			success:function(resp){
				if(resp == 1){
					alert_toast("Data successfully saved.",'success')
					setTimeout(function(){
						location.reload()
					},1000)
				}
			}
		})
	})
    $('.delete_comment').click(function(){
        _conf("Are you sure to delete this comment?","delete_comment",[$(this).attr('data-id')],'mid-large')
    })

    function delete_comment($id){
        start_load()
        $.ajax({
            url:'admin/ajax.php?action=delete_comment',
            method:'POST',
            data:{id:$id},
            success:function(resp){
                if(resp==1){
                    alert_toast("Data successfully deleted",'success')
                    setTimeout(function(){
                        location.reload()
                    },1500)

                }
            }
        })
    }

</script>