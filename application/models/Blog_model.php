<?

	class blog_model extends CI_Model
	{
	
		function getLatestPosts($total = 10)
		{
		
			return $this->db->query("SELECT * FROM blogs ORDER BY date DESC LIMIT {$total}")->result_array();
		
		}
		
		function getTotalArchivedPosts()
		{
		
			return $this->db->query("SELECT * FROM blogs ORDER BY date DESC")->num_rows();
		
		}
		
		function getArchivedPosts($page = 0, $limit = 999999)
		{
		
			return $this->db->query("SELECT * FROM blogs ORDER BY date DESC LIMIT {$page}, {$limit}")->result_array();
		
		}
		
		function getPost($id_or_url)
		{
		
			return $this->db->query("SELECT * FROM blogs WHERE (id = '{$id_or_url}' OR url = '{$id_or_url}') LIMIT 1")->row_array();
		
		}
		
		function totalComments($blog_id)
		{
		
			return $this->db->query("SELECT id FROM blog_comments WHERE blog_id = {$blog_id} AND
					blog_comments.approved = 1")->num_rows();
		
		}
		
		function getComments($blog_id)
		{
		
			return $this->db->query
			("
			
				SELECT 
					blog_comments.*,
					members.username
				
				FROM 
					blog_comments 
					
				JOIN members ON members.id = blog_comments.member_id
					
				WHERE 
					blog_comments.blog_id = {$blog_id} AND 
					blog_comments.approved = 1
			
			")->result_array();
		
		}
	
	}