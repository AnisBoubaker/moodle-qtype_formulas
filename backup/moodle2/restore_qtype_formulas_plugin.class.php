<?php
/**
 * For course/quiz restore
 *
 * @copyright &copy; 2010 Hon Wai, Lau
 * @author Hon Wai, Lau <lau65536@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 * @package questionbank
 * @subpackage questiontypes
 */
 
defined('MOODLE_INTERNAL') || die();

/**
 * restore plugin class that provides the necessary information
 * needed to restore one formulas qtype plugin
 */
class restore_qtype_formulas_plugin extends restore_qtype_plugin {

    /**
     * Returns the paths to be handled by the plugin at question level
     */
    protected function define_question_plugin_structure() {

        $paths = array();
        
        // This qtype uses don't question_answers, question_formulas_answers are differents

        // Add own qtype stuff
        $elename = 'formulasanswer';
        $elepath = $this->get_pathfor('/formulas_answers/formulas_answer'); // we used get_recommended_name() so this works
        $paths[] = new restore_path_element($elename, $elepath);
        $elename = 'formulas';
        $elepath = $this->get_pathfor('/formulas'); // we used get_recommended_name() so this works
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths; // And we return the interesting paths
    }

    /**
     * Process the qtype/formulas element
     */
    public function process_formulas($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        // Detect if the question is created or mapped
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // If the question has been created by restore, we need to create its question_formulas too
        if ($questioncreated) {
            // Adjust some columns
            $data->questionid = $newquestionid;
            // Insert record
            $newitemid = $DB->insert_record('question_formulas', $data);
            // Create mapping (needed for decoding links)
            $this->set_mapping('question_formulas', $oldid, $newitemid);
        } else {
            // Nothing to remap if the question already existed
        }
    }
    
    /**
     * Process the qtype/formulasanswer element
     */
    public function process_formulasanswer($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Detect if the question is created or mapped
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // If the question has been created by restore, we need to create its question_formulas_answers too
        if ($questioncreated) {
            // Adjust some columns
            $data->questionid = $newquestionid;
            // Insert record
            $newitemid = $DB->insert_record('question_formulas_answers', $data);
            // Create mapping
            $this->set_mapping('question_formulas_answer', $oldid, $newitemid);
        } else {
            // Nothing to remap if the question already existed
        }
    }
    
}
