<?php echo $newClassNamespace ? 'namespace ' . $newClassNamespace . ';' : ''; ?>

trait {{newClassName}} {
    protected ${{head}}s;

    public function get{{ucfirst($head)}}s() {
        return $this->{{head}}s;
    }


    public function add{{ucfirst($head)}}(${{head}})
    {
        $this->{{head}}s[] = ${{head}};

        return $this;
    }

    public function remove{{ucfirst($head)}}(${{head}})
    {
        $this->{{head}}s->removeElement(${{head}});
    }
}