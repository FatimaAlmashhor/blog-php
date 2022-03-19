<?php

namespace Post;

use Database\DBConnection;
use PDO;

require_once __DIR__ . "/../../vendor/autoload.php";

/**
 * Class Posts
 */
class Management
{

    public int $postId;
    public string $postTitle;
    public string $postBody;
    // public string $isPublished;
    private object $db;
    private int $userId;

    /**
     * Posts constructor.
     */
    public function __construct()
    {
        session_start();
        // $this->userId = intval($_SESSION["id"]);
        $this->db = new DBConnection();
    }

    /**
     * Create new post in database
     * @param string $title
     * @param string $body
     * @param string $isPublished
     */
    public function addPost(string $title, string $body): void
    {
        // Prepare the sql statement
        $this->db->query("INSERT INTO `blogs` (`blog_title`, `blog_body`, `created_at`, `is_active`) VALUES (:postTitle, :postBody,:created, true)");
        $this->db->bind(":postTitle", $title, PDO::PARAM_STR);
        $this->db->bind(":postBody", $body, PDO::PARAM_STR);
        $this->db->bind(":created", date("l jS \of F Y h:i:s A"), PDO::PARAM_STR_CHAR);

        // Execute the statement
        if ($this->db->execute()) {
            header("location: ./posts.php?newPostStatus=1");
            die();
        } else {
            header("location: ./posts.php?newPostStatus=2");
            die();
        }
    }

    /**
     * Delete the post in database
     * @param int $id
     */
    public function deletePost(int $id): void
    {
        $this->db->query("DELETE FROM `blogs` WHERE `blog_id`=:postId");
        $this->db->bind(":postId", $id, PDO::PARAM_INT);

        if ($this->db->execute()) {
            header("location: ./posts.php?deletePostStatus=1");
            die();
        } else {
            header("location: ./posts.php?deletePostStatus=2");
            die();
        }
    }

    /**
     * Get post
     * @param int $id
     */
    public function getPost(int $id): void
    {
        $this->db->query("SELECT `title`, `body`, `published` FROM `posts` WHERE `id`=:id");
        $this->db->bind(":id", $id, PDO::PARAM_INT);
        $this->db->execute();
        $result = $this->db->fetch();
        $this->postId = $id;
        $this->postTitle = $result->title;
        $this->postBody = $result->body;
        $this->isPublished = $result->published;
    }

    /**
     * Update the post
     * @param string $title
     * @param string $body
     * @param string $isPublished
     * @param int $postId
     */
    public function updatePost(string $title, string $body, string $isPublished, int $postId): void
    {
        $this->db->query("UPDATE `blogs` SET `blog_title` = :title, `blog_body` = :body, `published` = :published WHERE `blog_id` = :id");
        $this->db->bind(":title", $title, PDO::PARAM_STR);
        $this->db->bind(":body", $body, PDO::PARAM_STR);
        $this->db->bind(":published", $isPublished, PDO::PARAM_STR);

        if ($this->db->execute()) {
            header("location: ./posts.php?updatePostStatus=1");
            die();
        } else {
            header("location: ./posts.php?updatePostStatus=2");
            die();
        }
    }

    /**
     * Print messages received from request
     * @param string $type
     * @param int $errorCode
     */
    public function printMessages(string $type, int $errorCode): void
    {
        $errorMessage = "";
        if ($type == "newPost") {
            switch ($errorCode) {
                case 1:
                    $errorMessage = "Post Created Successfully";
                    break;
                case 2:
                    $errorMessage = "Something goes wrong";
                    break;
            }
        } elseif ($type == "deletePost") {
            switch ($errorCode) {
                case 1:
                    $errorMessage = "Post Deleted Successfully";
                    break;
                case 2:
                    $errorMessage = "Something goes wrong";
                    break;
            }
        } elseif ($type == "updatePost") {
            switch ($errorCode) {
                case 1:
                    $errorMessage = "Post Updated Successfully";
                    break;
                case 2:
                    $errorMessage = "Something goes wrong";
                    break;
            }
        }
?>
<div class="pt-3 pb-3 text-center text-white bg-<?php echo ($errorCode == 1) ? "success" : "danger" ?> w-100 h-auto">
    <b><?php echo $errorMessage; ?></b>
</div>
<?php
    }
}