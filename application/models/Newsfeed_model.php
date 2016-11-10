<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Newsfeed_model extends CRM_Model
{
    public $post_likes_limit = 6;
    public $post_comment_likes_limit = 6;
    public $post_comments_limit = 6;
    public $newsfeed_posts_limit = 10;
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Stick post to top
     * @param  mixed $id post id
     * @return boolean
     */
    public function pin_post($id)
    {

        $this->db->where('postid', $id);
        $this->db->update('tblposts', array(
            'pinned' => 1,
            'datepinned' => date('Y-m-d H:i:s')
        ));

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Unpin post from top
     * @param  mixed $id post id
     * @return boolean
     */
    public function unpin_post($id)
    {

        $this->db->where('postid', $id);
        $this->db->update('tblposts', array(
            'pinned' => 0,
            'datepinned' => null
        ));

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Get post all attachments
     * @param  mxied $id post id
     * @return array
     */
    public function get_post_attachments($id)
    {
        $this->db->where('postid', $id);
        return $this->db->get('tblpostattachments')->result_array();
    }

    /**
     * Get all post likes
     * @param  mixed $id post id
     * @return array
     */
    public function get_post_likes($id){
        $this->db->select();
        $this->db->from('tblpostlikes');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblpostlikes.userid', 'left');
        $this->db->where('userid !=', get_staff_user_id());
        $this->db->where('postid', $id);
        $this->db->order_by('dateliked', 'asc');
        return $this->db->get()->result_array();
    }
    /**
     * Get post likes - used in modal with loading data
     * @param  mixed $offset page
     * @param  mixed $postid post id
     * @return array
     */
    public function load_likes_modal($offset, $postid)
    {
        $offset = ($offset * $this->post_likes_limit);
        $this->db->where('postid', $postid);
        $this->db->order_by('dateliked', 'desc');
        $this->db->limit($this->post_likes_limit, $offset);
        return $this->db->get('tblpostlikes')->result_array();
    }

        /**
     * Get post comments - used in modal with loading data
     * @param  mixed $offset page
     * @param  mixed $commentid commentid id
     * @return array
     */
    public function load_comment_likes_model($offset, $commentid)
    {
        $offset = ($offset * $this->post_comment_likes_limit);
        $this->db->where('commentid', $commentid);
        $this->db->order_by('dateliked', 'desc');
        $this->db->limit($this->post_comment_likes_limit, $offset);
        return $this->db->get('tblcommentlikes')->result_array();
    }

    /**
     * Load news feed / home
     * @param  mixed $offset page
     * @return array
     */
    public function load_newsfeed($offset)
    {

        $offset = ($offset * $this->newsfeed_posts_limit);

        $this->db->where('pinned', 0);
        $this->db->order_by('datecreated', 'desc');
        if ($this->input->post('postid')) {
            $this->db->where('postid', $this->input->post('postid'));
        } else {
            $this->db->limit($this->newsfeed_posts_limit, $offset);
        }

        return $this->db->get('tblposts')->result_array();
    }

    /**
     * Get all sticked posts to top
     * @return array
     */
    public function get_pinned_posts()
    {

        $this->db->where('pinned', 1);
        $this->db->order_by('datepinned', 'asc');
        return $this->db->get('tblposts')->result_array();
    }

    /**
     * Get post single post by id
     * @param  mixed $id postid
     * @return object
     */
    public function get_post($id)
    {
        $this->db->where('postid', $id);
        return $this->db->get('tblposts')->row();
    }

    /**
     * Get post comment by id
     * @param  mixed $id comment id
     * @return objetc
     */
    public function get_comment($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('tblpostcomments')->row();
    }

    /**
     * Add new post to database
     * @param array $data post data
     */
    public function add($data)
    {
        unset($data['null']);
        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['content']     = nl2br($data['content']);
        $data['creator']     = get_staff_user_id();

        if (!isset($data['visibility'])) {
            $data['visibility'] = 'all';
        } else {
            if ($data['visibility'][0] != 'all') {

                $data['visibility'] = implode(':', $data['visibility']);
            } else {
                $data['visibility'] = 'all';
            }
        }



        $this->db->insert('tblposts', $data);
        return $this->db->insert_id();
    }

    /**
     * Like post
     * @param  mixed $id postid
     * @return boolean
     */
    public function like_post($id)
    {
        if ($this->user_liked_post($id)) {
            return;
        }
        $this->db->insert('tblpostlikes', array(
            'postid' => $id,
            'userid' => get_staff_user_id(),
            'dateliked' => date('Y-m-d H:i:s')
        ));
        $likeid = $this->db->insert_id();
        if ($likeid) {
            $post = $this->get_post($id);
            if ($post->creator != get_staff_user_id()) {
                add_notification(array(
                    'description' => get_staff_full_name(get_staff_user_id()) . ' liked your post ' . substr($post->content, 0, 50) . '...',
                    'touserid' => $post->creator
                ));
            }
            return true;
        }

        return false;
    }

    /**
     * Unlike post
     * @param  mixewd $id post id
     * @return boolean
     */
    public function unlike_post($id)
    {
        $this->db->where('userid', get_staff_user_id());
        $this->db->where('postid', $id);
        $this->db->delete('tblpostlikes');

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Unlike post comment
     * @param  mixed $id     commentid
     * @param  mixed $postid post id
     * @return boolean
     */
    public function unlike_comment($id, $postid)
    {

        $this->db->where('userid', get_staff_user_id());
        $this->db->where('commentid', $id);
        $this->db->where('postid', $postid);
        $this->db->delete('tblcommentlikes');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Check if current user liked post
     * @param  mixed $id post id
     * @return mixed
     */
    public function user_liked_post($id)
    {
        $this->db->where('userid', get_staff_user_id());
        $this->db->where('postid', $id);
        return $this->db->get('tblpostlikes')->row();
    }

    /**
     * Check if current user liked comment
     * @param  mixed $id comment id
     * @return mixed
     */
    public function user_liked_comment($id)
    {
        $this->db->where('userid', get_staff_user_id());
        $this->db->where('commentid', $id);
        return $this->db->get('tblcommentlikes')->row();
    }

    /**
     * Add new post comment
     * @param array $data comment data
     */
    public function add_comment($data)
    {
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['userid']    = get_staff_user_id();
        $data['content']   = nl2br($data['content']);
        $this->db->insert('tblpostcomments', $data);
        if ($this->db->affected_rows() > 0) {
            $post = $this->get_post($data['postid']);
            if ($post->creator != get_staff_user_id()) {
                add_notification(array(
                    'description' => get_staff_full_name(get_staff_user_id()) . ' commented on your post ' . substr($post->content, 0, 50) . '...',
                    'touserid' => $post->creator
                ));
            }
            return true;
        }
        return false;
    }
    /**
     * Like post comment
     * @param  mixed $id     comment id
     * @param  mixed $postid post id
     * @return mixed
     */
    public function like_comment($id, $postid)
    {

        if ($this->user_liked_comment($id)) {
            return;
        }

        $data['dateliked'] = date('Y-m-d H:i:s');
        $data['userid']    = get_staff_user_id();
        $data['commentid'] = $id;
        $data['postid']    = $postid;

        $this->db->insert('tblcommentlikes', $data);
        if ($this->db->affected_rows() > 0) {
            $comment = $this->get_comment($id);
            if ($comment->userid != get_staff_user_id()) {
                add_notification(array(
                    'description' => get_staff_full_name(get_staff_user_id()) . ' liked your comment ' . substr($comment->content, 0, 50) . '...',
                    'touserid' => $comment->userid
                ));
            }
            return true;
        }
        return false;
    }

    /**
     * Remove post comment from database
     * @param  mixed $id     comment id
     * @param  mixed $postid post id
     * @return boolean
     */
    public function remove_post_comment($id, $postid)
    {
        // First check if this user created the comment

        if (total_rows('tblpostcomments', array(
            'postid' => $postid,
            'userid' => get_staff_user_id(),
            'id' => $id
        )) > 0) {
            $this->db->where('id', $id);
            $this->db->where('postid', $postid);
            $this->db->where('userid', get_staff_user_id());
            $this->db->delete('tblpostcomments');
            if ($this->db->affected_rows() > 0) {
                return true;
            }

            return false;
        }

        return false;
    }

    /**
     * Delete all and all connections
     * @param  mixed $postid post id
     * @return boolean
     */
    public function delete_post($postid)
    {

        // First check if this user creator of the post
        if (total_rows('tblposts', array(
            'postid' => $postid,
            'creator' => get_staff_user_id()
        )) > 0 || is_admin()) {
            $this->db->where('postid', $postid);
            $this->db->delete('tblposts');
            if ($this->db->affected_rows() > 0) {

                $this->db->where('postid', $postid);
                $this->db->delete('tblpostlikes');

                $this->db->where('postid', $postid);
                $this->db->delete('tblpostcomments');

                $this->db->where('postid', $postid);
                $this->db->delete('tblcommentlikes');

                $this->db->where('postid', $postid);
                $this->db->delete('tblpostattachments');

                if (is_dir(NEWSFEED_FOLDER . $postid)) {
                    delete_dir(NEWSFEED_FOLDER . $postid);
                }
                return true;

            }
        }
        return false;
    }

    /**
     * Get all comments from post / using loader
     * @param  mixed $postid psot id
     * @param  mixed $offset page
     * @return array
     */
    public function get_post_comments($postid, $offset)
    {
        $this->db->where('postid', $postid);
        $offset = ($offset * $this->post_comments_limit);
        $this->db->limit($this->post_comments_limit, $offset);
        return $this->db->get('tblpostcomments')->result_array();
    }
}
