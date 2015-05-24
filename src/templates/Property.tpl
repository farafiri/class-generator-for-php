<?php echo $newClassNamespace ? 'namespace ' . $newClassNamespace . ';' : ''; ?>

trait {{newClassName}} {
    protected ${{head}};

    public function get{{ucfirst($head)}}() {
        return $this->{{head}};
    }

    public function set{{ucfirst($head)}}(${{head}}) {
        $this->{{head}} = ${{head}};
        return $this;
    }
}