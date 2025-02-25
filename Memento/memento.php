<?php
session_start();

// Memento class stores a snapshot of the text state
class Memento {
    public $state;
    public function __construct($state) {
        $this->state = $state;
    }
}

// Caretaker manages the undo/redo history
class Caretaker {
    public $history = [];
    public $redoStack = [];

    // Save the current state if it's different from the last
    public function save($state) {
        if (empty($this->history) || end($this->history)->state !== $state) {
            $this->history[] = new Memento($state);
            $this->redoStack = []; // Clear redo stack when new state is saved
        }
    }

    // Undo to the previous state
    public function undo() {
        if (count($this->history) > 1) {
            $this->redoStack[] = array_pop($this->history);
            return end($this->history)->state;
        }
        return $this->history[0]->state ?? '';
    }

    // Redo to the next state
    public function redo() {
        if (!empty($this->redoStack)) {
            $this->history[] = array_pop($this->redoStack);
            return end($this->history)->state;
        }
        return end($this->history)->state ?? '';
    }
}

// Initialize session caretaker if not set
if (!isset($_SESSION['caretaker'])) {
    $caretaker = new Caretaker();
    $caretaker->save('');
    $_SESSION['caretaker'] = serialize($caretaker);
    $_SESSION['currentText'] = '';
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caretaker = unserialize($_SESSION['caretaker']);
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $caretaker->save($_POST['text'] ?? '');
        $_SESSION['currentText'] = $_POST['text'] ?? '';
    } elseif ($action === 'undo') {
        $_SESSION['currentText'] = $caretaker->undo();
    } elseif ($action === 'redo') {
        $_SESSION['currentText'] = $caretaker->redo();
    }

    $_SESSION['caretaker'] = serialize($caretaker);
    echo json_encode(['text' => $_SESSION['currentText']]);
    exit;
}

// Return current text state if requested
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(['text' => $_SESSION['currentText']]);
    exit;
}

