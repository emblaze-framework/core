"""
GIT PUSH
CREATED BY: REY MARK A. DIVINO
"""
#!/usr/bin/python3
# -*- coding: utf-8 -*-
import io
import subprocess

# git status
def git_status():
    proc = subprocess.Popen(["git", "status"], stdout=subprocess.PIPE)
    for line in io.TextIOWrapper(proc.stdout, encoding="utf-8"):  # or another encoding
      # do something with line
        print(line)

def git_add():
    proc = subprocess.Popen(["git", "add", "."], stdout=subprocess.PIPE)
    for line in io.TextIOWrapper(proc.stdout, encoding="utf-8"):  # or another encoding
      # do something with line
        print(line)

def git_commit(commit_message):
    proc = subprocess.Popen(["git", "commit", "-S","-m",commit_message], stdout=subprocess.PIPE)
    for line in io.TextIOWrapper(proc.stdout, encoding="utf-8"):  # or another encoding
      # do something with line
        print(line)

def git_push():
    proc = subprocess.Popen(["git", "push"], stdout=subprocess.PIPE)

    for line in io.TextIOWrapper(proc.stdout, encoding="utf-8"):  # or another encoding
      # do something with line
        print(line)

        # git commit -S -m "update homepage"

def add_tag():
  yesno = str(input("Add tag?[Yes/No]: "))
  if yesno.lower() == "y" or yesno.lower() == "yes":
    tagVersion = str(input("input tag e.g. 1.0.1: "))

    proc = subprocess.Popen(["git", "tag", tagVersion], stdout=subprocess.PIPE)
    for line in io.TextIOWrapper(proc.stdout, encoding="utf-8"):  # or another encoding
      # do something with line
        print(line)

    proc2 = subprocess.Popen(["git", "push", "--tag"], stdout=subprocess.PIPE)
    for line2 in io.TextIOWrapper(proc2.stdout, encoding="utf-8"):  # or another encoding
      # do something with line
        print(line2)
    exit()
  else:
    pass

# Run this code.
if __name__ == "__main__":
    
    commit_message = str(input("Input Commit Message?: "))
    # git status
    git_status()

    # git add
    git_add()

    # git commit
    git_commit(commit_message)

    # Push
    git_push()

    # Add Tag
    add_tag()
    

