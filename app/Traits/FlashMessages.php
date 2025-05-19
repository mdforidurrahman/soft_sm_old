<?php

namespace App\Traits;

trait FlashMessages
{
    public function flashSuccess($message)
    {
        return $this->flash('success', $message);
    }

    public function flashError($message)
    {
        return $this->flash('error', $message);
    }

    public function flashWarning($message)
    {
        return $this->flash('warning', $message);
    }

    public function flashInfo($message)
    {
        return $this->flash('info', $message);
    }

    private function flash($type, $message)
    {
        session()->flash($type, $message);
        return $this;
    }

    public function withSuccess($message)
    {
        return redirect()->back()->with('success', $message);
    }

    public function withError($message)
    {
        return redirect()->back()->with('error', $message);
    }

    public function withWarning($message)
    {
        return redirect()->back()->with('warning', $message);
    }

    public function withInfo($message)
    {
        return redirect()->back()->with('info', $message);
    }
}
