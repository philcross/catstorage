### Running Via Console

I've avoided using 3rd party packages within the application, but for console access, I've used the Symfony console package
for ease. 

To run via console, just run `php console`, which will bring up a list of available commands:

 - `php console cats:list-directories [/sub-directory-path="/"]`
 
    This will list all the directories within a given directory path. By default, it will list directories in the 
    filesystem root, but you can go deeper by specifying the path (relative to the root). For example, the root directory
    in this application is the images directory. If you had a subdirectory called `dogs`, you can list directories within
    `dogs` with `php console:cat-directories dogs`
    
    I've tried to keep the command easy to use, while also demonstrating the filesystem implementation, so the command
    will ask you questions after displaying the table of directories (such as would you like to create a new directory, etc)
    
 - `php console cats:list-files [/sub-directory-path="/"]`
    
    Exactly the same as the above command, but instead lists the files within a given directory.
    

These are the only 2 commands I've added, to demonstrate the filesystem, but both commands have a variety of additional
tasks they can do. Creating directories, creating files, renaming, deleting etc.

### Approach

I tried a couple approaches at first, but the one that stuck was to just use a TDD approach to create an implementation 
of the filesystem interface which was directly coupled to the local filesystem. 

Once this was done, It was (already) clear
that the storage mechanism needed to be decoupled from the filesystem implementation, so used the adapter pattern to 
create a LocalStorage adapter, again using TDD. The methods I created on the adapter closely matched methods on the interface, but not 100%.
I extracted the AdapterInterface from the first LocalStorage adapter.
Ideally, I'd extract the interface after a second adapter, but for the purposes of time, and the fact I knew what I was
aiming for I pulled the interface out early.

At this point, I had an adapter, and a filesystem implementation, both with separate unit tests, and I began replacing method
logic on the filesystem implementation with calls to the adapter. 

After finally cleaning up all the logic between the repository and the adapter, I added an ArrayStorage adapter, just to 
demonstrate the ability to create decoupled adapters fine. 

All the tests for the adapters are in a single abstract test file, and each adapter has a test file which extends it, and the sole
purpose of the adapter test file ,is to return an instance of the adapter.

If we need to add a new adapter, the only test we need to create is one with a method to return the adapter instance, and
the existing tests will ensure compatibility with the other adapters.

I've tried to commit it as best as I can to show the approaches I took, from initial TDD approaches on adapters and interfaces
to the refactorings i did to pull out the adapters. 


### Thoughts

 - It took me a bit of time to get going - I've worked previously on filesystem applications, so kind of knew the direction
   I wanted to go, but the 3 given interfaces made it feel a bit weird. If I was given this as a real life project, 
   the existing interfaces would feel like a code smell - abstracting too soon.
   Instead, I would have written some basic logic to get what I wanted, then gradually refactored to find logic patterns,
   then pull out the interfaces and abstractions.
  - It feels a bit weird that the filesystem interface would have a method to create the root directory. At the point 
  where the filesystem was instantiated, I would have probably already know the root.
  - I would have prefered for the DirectoryInterface and FileInterface interfaces to be immutable, and remove the setters.
   Having the setters available seems to make it slightly confusing, for example, if you want to create a new file, the
   FilesystemInterface requires the file object, and the parent directory. But the file object allows you to set the parent
   directory directory, so the filesystem implementation didn't really need the parent Directory argument.
   Also, when creating a new file, without the proper documentation for the application, new developers might think they're
   required to complete all the setters. 
   I've added a static constructor method to make it clearer when a file is currently unsaved and due to be created, and
   when a file already exists.
  - The attribute for created time on files is a bit weird on Linux - The File implementation wants a "created" time, but
  on Linux (php), it only returns modified time, access time, and change time.   

### Given more time

 - If given more time, I would have implemented filesystem permission checking and more exhaustive error detection on
   the local storage adapter. 
 - Created better value objects for the normalized paths.
