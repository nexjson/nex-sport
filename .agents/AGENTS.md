# NEX-Sport — Agent Rules

## The Four Rules of Coding Practice

These rules apply to every task in this project without exception. Before writing any code, confirm you understand and are following all four.

---

### 1. Think Before Coding

State your assumptions out loud before starting.
If the request is ambiguous, ask — do not guess.
If a simpler approach exists, push back and explain why.
Stop when confused, name what is unclear, and wait for clarification. Do **not** pick one interpretation and run with it.

> **Before writing code, always answer:** What exactly is being asked? What assumptions am I making? Is there a simpler way?

---

### 2. Simplicity First

Write the **minimum** code that solves the problem.
No speculative abstractions. No added flexibility nobody asked for.
No premature generalization or "just in case" layers.

> **The test:** Would a senior engineer look at this and call it overcomplicated? If yes, simplify it.

---

### 3. Surgical Changes

Touch **only** what the task requires.
Do not improve neighboring code. Do not refactor what is not broken.
Do not clean up unrelated formatting, rename unrelated variables, or restructure files that were not part of the request.

> **Every changed line must trace directly back to the original request.** If you cannot explain why a line changed, revert it.

---

### 4. Goal-Driven Execution

Turn vague instructions into **verifiable targets** before writing a line of code.

| Vague Instruction | Verifiable Target |
|-------------------|-------------------|
| "Add validation" | Write tests for invalid inputs, then make them pass |
| "Make it faster" | Define what metric proves it is faster, then optimize |
| "Fix the bug" | Write a test that reproduces the bug, then fix the code |
| "Add a feature" | Define the acceptance criteria, then implement |

> Do not start coding until you can state: *"This task is done when [specific, measurable outcome]."*
