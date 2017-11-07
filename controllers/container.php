<?php

require_once 'app/controllers/plugin_controller.php';

class ContainerController extends PluginController {

    function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
        Navigation::activateItem("/course/tresor");
        PageLayout::addScript($this->plugin->getPluginURL()."/assets/Tresor.js");
        PageLayout::addScript($this->plugin->getPluginURL()."/assets/openpgp.js");
        PageLayout::addScript("jquery/jquery.tablesorter-2.22.5.js");
        PageLayout::addStylesheet($this->plugin->getPluginURL()."/assets/Tresor.css");
    }

    public function index_action() {
        if ($GLOBALS['perm']->have_perm("admin")) {
            PageLayout::postMessage(MessageBox::info(_("Sie sind Admin und nicht Mitglied dieser Veranstaltung. Die vorliegenden Dokumente sind nicht f�r Sie verschl�sselt.")));
        }
        $this->foreign_user_public_keys = TresorUserKey::findForSeminar($_SESSION['SessionSeminar']);
        $this->coursecontainer = TresorContainer::findBySQL("seminar_id = ? ORDER BY name", array($_SESSION['SessionSeminar']));
    }

    public function details_action($tresor_id) {
        $this->container = new TresorContainer($tresor_id);
        if (!$GLOBALS['perm']->have_studip_perm("autor", $this->container['seminar_id'])) {
            throw new AccessDeniedException();
        }
        $this->foreign_user_public_keys = TresorUserKey::findForSeminar($this->container['seminar_id']);
    }

    public function store_action($tresor_id = null) {
        $this->container = new TresorContainer($tresor_id);
        if (($tresor_id && !$GLOBALS['perm']->have_studip_perm("autor", $this->container['seminar_id']))
                || (!$tresor_id && !$GLOBALS['perm']->have_studip_perm("autor", $_SESSION['SessionSeminar']))) {
            throw new AccessDeniedException();
        }
        if (Request::isPost()) {
            $this->container['name'] = Request::get("name");
            $this->container['mime_type'] = Request::get("mime_type", "text/plain");
            $this->container['encrypted_content'] = Request::get("encrypted_content");
            $this->container['last_user_id'] = User::findCurrent()->id;
            if ($this->container->isNew()) {
                $this->container['seminar_id'] = $_SESSION['SessionSeminar'];
            }
            $this->container->store();
            PageLayout::postMessage(MessageBox::success(_("Daten wurden verschl�sselt und gespeichert.")));
            $this->redirect("container/details/".$this->container->getId());
        }
    }

    public function create_action()
    {
        if (Request::isPost()) {
            $this->container = new TresorContainer();
            $this->container['seminar_id'] = $_SESSION['SessionSeminar'];
            $this->container['name'] = Request::get("name");
            $this->container['last_user_id'] = $GLOBALS['user']->id;
            $this->container['encrypted_content'] = "";
            $this->container->store();
            PageLayout::postSuccess(_("Neuen Text initialisiert"));
            $this->redirect("container/details/".$this->container->getId());
        }
    }

    public function delete_action($tresor_id) {
        if (Request::isPost()) {
            $this->container = new TresorContainer($tresor_id);
            if (!$GLOBALS['perm']->have_studip_perm("tutor", $this->container['seminar_id'])) {
                throw new AccessDeniedException();
            }
            $this->container->delete();
            PageLayout::postSuccess(_("Text wurde gel�scht."));
            $this->redirect("container/index");
        }
    }

}